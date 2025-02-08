<form method="post" id="videoPost" class="flexo flexCom" enctype="multipart/form-data" action="/create">
    <script src="/assets/js/verifyString.js"></script>
    <script src="/assets/js/uploadProgress.js"></script>
    <div class="subtitle-h-2">
        <b class="font-mid">• Tamanho Máximo: 250mb</b>
        <br>
        <b class="font-mid">• Limite de 3 vídeos por dia</b>
    </div>
    <br>
    <input type="file" name="video-up" style="display:none;" id="video-up" placeholder="Clique para fazer upload do vídeo.">
    <div class="flexo">
        <label class="upLabel font-mid-large" for="video-up">Arquivo</label>
        <div class="progress-bar flexRow">
            <div class="progress-bar-inside1">
            </div>
        </div>
    </div>
    <div class="flexo flexCom">
        <span class="progress-bar-message"></span>
        <b style="margin-top: 5px;">Nome do Arquivo: <span id="nameFile"></span></b>
    </div>
    <br>
    <label class="tLabel1" for="video-title">Título:</label>
    <input id="inputTo50" type="text" name="video-title" class="round-b" required>
    <b><span id="textTo50"></span>/50</b>
    <br>
    <label class="tLabel1" for="video-description">Descrição:</label>
    <textarea class="textarea1 round-b" id="inputTo200" name="video-description"></textarea>
    <b><span id="textTo200"></span>/200</b>
    <br>
    <input type="hidden" name="form-type" value="video">
    <input class="ser-button round-b" type="submit" value="Publicar">
</form>
