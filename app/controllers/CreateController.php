<?php
namespace app\controllers;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use app\database\ConnectionSQL;
use app\database\ConnectionRedis;
use FFMpeg\FFMpeg;
use FFMpeg\Format\Video\X264;
use FFMpeg\Coordinate\TimeCode;
class CreateController {
    private $videoBasePath;
    private $thumbnailBasePath;
    private $newBitrate = 0;
    
    public function __construct() {
        $this->videoBasePath = 'C:/Users/Usuario/Desktop/gff/videos/';
        $this->thumbnailBasePath = 'C:/Users/Usuario/Desktop/gff/thumbnails/';
        
        if (!file_exists($this->videoBasePath)) {
            mkdir($this->videoBasePath, 0777, true);
        }
        if (!file_exists($this->thumbnailBasePath)) {
            mkdir($this->thumbnailBasePath, 0777, true);
        }
    }
    
    public function create(Request $request, Response $response) {
        ini_set('memory_limit', '250M');
        ini_set('post_max_size', '250M');
        ini_set('upload_max_filesize', '250M');
        ini_set('max_execution_time', '3600');
        ini_set('max_input_time', '3600');
        ini_set('output_buffering', 'Off');
        ini_set('zlib.output_compression', 'Off');
        set_time_limit(3600);
        $message = '';
        $formData = $request->getParsedBody();
        $formType = $formData['form-type'] ?? null;
        $userId = htmlspecialchars($_SESSION['user-info']['userId'] ?? '', ENT_QUOTES, 'UTF-8');
        if (!isset($userId) || $_SESSION['type-user'] !== 'user') {
            return $response->withHeader('Location', '/login')->withStatus(302);
        }
        $pdo = ConnectionSQL::connect();
        if ($formType === 'postagem') {
            $this->handlePost($formData, $pdo, $userId, $message);
            return $response;
        } elseif ($formType === 'video') {
            return $this->handleVideo($formData, $pdo, $userId, $response, $message);
        }
        view('create', [
            'title' => 'Criar',
            'hh' => null,
            'message' => $message,
        ]);
        return $response;
    }
    
    private function handlePost(array $formData, $pdo, $userId, &$message) {
        $title = $formData['post-title'] ?? '';
        $content = $formData['post-content'] ?? '';
        if (strlen($content) > 200 || strlen($title) > 50) {
            $message = "Limite excedido de caracteres.";
            return;
        }
        try {
            $query = "INSERT INTO posts (id, title, content, idAuthor, post_url) VALUES (:id, :title, :content, :idAuthor, :post_url)";
            $stmt = $pdo->prepare($query);
            $stmt->execute([
                'id' => generateRandomString(13),
                'title' => $title,
                'content' => $content,
                'idAuthor' => $userId,
                'post_url' => generateRandomString(12)
            ]);
            ConnectionRedis::setData('users', $userId, 'last_activity', [date("Y-m-d", time())]);
            header('Location: /');
            exit;
        } catch (\PDOException $e) {
            $message = "Erro ao criar postagem: " . $e->getMessage();
            error_log("Erro na criação do post: " . $e->getMessage());
        }
    }
    
    private function handleVideo(array $formData, $pdo, $userId, Response $response, &$message) {
        while (ob_get_level() > 0) {
            ob_end_clean();
        }
        
        try {
            $today = date('Y-m-d');
            $dailyLimit = 3;
            $query = "SELECT COUNT(*) as upload_count FROM videos WHERE idAuthor = :idAuthor AND DATE(created_at) = :today";
            $stmt = $pdo->prepare($query);
            $stmt->execute(['idAuthor' => $userId, 'today' => $today]);
            $uploadCount = $stmt->fetchColumn();
            if ($uploadCount >= $dailyLimit) {
                $message = 'Você já atingiu o limite diário de 3 uploads de vídeo.';
                return $this->sendJsonResponse($response, ['error' => $message], 400);
            }
            $uploadedFile = $_FILES['video-up'] ?? null;
            $title = $formData['video-title'] ?? '';
            $description = $formData['video-description'] ?? '';
            if (strlen($title) > 50 || strlen($description) > 200) {
                $message = "Limite de caracteres atingido.";
                return $this->sendJsonResponse($response, ['error' => $message], 400);
            }
            if (!$uploadedFile || $uploadedFile['error'] !== UPLOAD_ERR_OK) {
                throw new \Exception($this->getUploadErrorMessage($uploadedFile['error'] ?? UPLOAD_ERR_NO_FILE));
            }
            if ($uploadedFile['size'] > 250000000) {
                $message = "Limite de tamanho excedido.";
                return $this->sendJsonResponse($response, ['error' => $message], 400);
            }
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_file($finfo, $uploadedFile['tmp_name']);
            finfo_close($finfo);
            if (!in_array($mimeType, ['video/mp4', 'video/quicktime', 'video/x-msvideo'])) {
                throw new \Exception('Tipo de arquivo não suportado.');
            }
            $this->checkDiskSpace($uploadedFile['size']);
            $tempDir = sys_get_temp_dir() . '/' . uniqid('video_');
            if (!file_exists($tempDir)) {
                mkdir($tempDir, 0777, true);
            }
            $videoName = uniqid() . '_' . preg_replace('/[^a-zA-Z0-9.]/', '_', $uploadedFile['name']);
            $videoPath = $this->videoBasePath . $videoName;
            $thumbnailPath = $this->thumbnailBasePath . $videoName . '.jpg';
            $this->uploadFileInChunks($uploadedFile['tmp_name'], $videoPath);
            $ffmpeg = FFMpeg::create([
                'ffmpeg.binaries'  => __DIR__ . '/../../bin/ffmpeg.exe',
                'ffprobe.binaries' => __DIR__ . '/../../bin/ffprobe.exe',
                'timeout'          => 3600,
                'ffmpeg.threads'   => 2,
                'temporary_directory' => $tempDir,
            ]);
            $video = $ffmpeg->open($videoPath);
            $duration = $video->getFFProbe()->format($videoPath)->get('duration');
            
            $dimensions = $video->getFFProbe()
                ->streams($videoPath)
                ->videos()
                ->first()
                ->getDimensions();
            
            $height = $dimensions->getHeight();
            $originalBitrate = $video->getFFProbe()->format($videoPath)->get('bit_rate');
            $format = new X264('aac');
            if ($height >= 720) {
                $this->newBitrate = intval($originalBitrate * 0.8 / 1000);
                $format
                    ->setKiloBitrate($this->newBitrate)
                    ->setAudioKiloBitrate(64);
            } else {
                $this->newBitrate = intval($originalBitrate * 0.6 / 1000);
                $format
                    ->setKiloBitrate($this->newBitrate)
                    ->setAudioKiloBitrate(58);
            }
            $compressedPath = $this->videoBasePath . 'compressed_' . $videoName;
            
            $format->on('progress', function ($video, $format, $percentage) use ($response) {
                // Não enviar resposta aqui, apenas armazenar progresso
                $progress = 40 + ($percentage * 0.5);
                // Não fazer flush aqui
            });
            $video->save($format, $compressedPath);
            
            $originalSize = filesize($videoPath);
            $compressedSize = filesize($compressedPath);
            
            if ($compressedSize >= $originalSize) {
                $format->setAdditionalParameters([
                    '-preset', 'veryslow',
                    '-crf', '32',
                    '-maxrate', ($this->newBitrate * 0.8) . 'k',
                    '-bufsize', ($this->newBitrate * 1.5) . 'k',
                    '-rc-lookahead', '60',
                ]);
                
                $video->save($format, $compressedPath);
                
                if (filesize($compressedPath) >= $originalSize) {
                    throw new \Exception('Não foi possível comprimir o vídeo para um tamanho menor.');
                }
            }
            $thumbTime = $duration * 0.50;
            $video->frame(TimeCode::fromSeconds($thumbTime))->save($thumbnailPath);
            $query = "INSERT INTO videos (id, title, idAuthor, file_url, file_name, file_thumbnail, description) 
                     VALUES (:id, :title, :idAuthor, :file_url, :file_name, :file_thumbnail, :description)";
            $stmt = $pdo->prepare($query);
            $stmt->execute([
                'id' => generateRandomString(12),
                'title' => $title,
                'idAuthor' => $userId,
                'file_url' => $compressedPath,
                'file_name' => $videoName,
                'file_thumbnail' => $thumbnailPath,
                'description' => $description
            ]);
            unlink($videoPath);
            
            // Limpar qualquer saída em buffer antes de enviar a resposta
            if (ob_get_level() > 0) {
                ob_end_clean();
            }
            return $this->sendJsonResponse($response, [
                'progress' => 100,
                'status' => 'completed',
                'message' => 'Upload concluído com sucesso!'
            ]);
        } catch (\Exception $e) {
            // Limpar buffer antes de enviar resposta de erro
            if (ob_get_level() > 0) {
                ob_end_clean();
            }
            
            $this->handleError($e, $videoPath ?? null, $compressedPath ?? null, $tempDir);
            return $this->sendJsonResponse($response, [
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    private function uploadFileInChunks($source, $destination, $chunkSize = 1048576) {
        $in = fopen($source, 'rb');
        $out = fopen($destination, 'wb');
        while (!feof($in)) {
            $chunk = fread($in, $chunkSize);
            fwrite($out, $chunk);
            
            if (ob_get_level() > 0) {
                ob_end_clean();
            }
            ob_start();
            flush();
            $this->checkClientConnection();
        }
        fclose($in);
        fclose($out);
    }
    
    private function sendJsonResponse(Response $response, array $data, int $status = 200): Response {
        $response->getBody()->write(json_encode($data));
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withHeader('Cache-Control', 'no-cache')
            ->withStatus($status);
    }
    
    private function handleError(\Exception $e, $videoPath, $compressedPath, $tempDir) {
        error_log('Erro no upload de vídeo: ' . $e->getMessage());
        error_log('Stack trace: ' . $e->getTraceAsString());
        if ($videoPath && file_exists($videoPath)) {
            unlink($videoPath);
        }
        if ($compressedPath && file_exists($compressedPath)) {
            unlink($compressedPath);
        }
        if ($tempDir && is_dir($tempDir)) {
            $files = glob($tempDir . '/*');
            foreach ($files as $file) {
                unlink($file);
            }
            rmdir($tempDir);
        }
    }
    
    private function checkDiskSpace($fileSize) {
        $freeSpace = disk_free_space($this->videoBasePath);
        $requiredSpace = $fileSize * 2.5;
        if ($freeSpace < $requiredSpace) {
            throw new \Exception('Espaço em disco insuficiente para processar o vídeo.');
        }
    }
    
    private function checkClientConnection() {
        if (connection_status() !== CONNECTION_NORMAL) {
            throw new \Exception('Conexão com o cliente perdida.');
        }
    }
    
    private function getUploadErrorMessage($code) {
        switch ($code) {
            case UPLOAD_ERR_INI_SIZE:
                return 'O arquivo enviado excede o limite permitido.';
            case UPLOAD_ERR_FORM_SIZE:
                return 'O arquivo enviado excede o limite do formulário.';
            case UPLOAD_ERR_PARTIAL:
                return 'O arquivo foi apenas parcialmente carregado.';
            case UPLOAD_ERR_NO_FILE:
                return 'Nenhum arquivo foi enviado.';
            case UPLOAD_ERR_NO_TMP_DIR:
                return 'Pasta temporária ausente.';
            case UPLOAD_ERR_CANT_WRITE:
                return 'Falha ao gravar arquivo em disco.';
            case UPLOAD_ERR_EXTENSION:
                return 'Uma extensão PHP interrompeu o upload do arquivo.';
            default:
                return 'Erro desconhecido no upload.';
        }
    }
}