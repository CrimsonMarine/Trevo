<script>
    document.addEventListener('DOMContentLoaded', function() {
        let currentStatus = 'offline';
        const selectElement = document.querySelector('select[name="status"]');
        selectElement.addEventListener('click', () => {
            if (selectElement.classList.contains('clicked')) {
                selectElement.classList.remove('clicked');
            } else {
                selectElement.classList.add('clicked');
            }
        });

        document.addEventListener('click', (e) => {
            if (!selectElement.contains(e.target)) {
                selectElement.classList.remove('clicked');
            }
        });
        if (document.body.contains(selectElement)) {
            
            function updateActivity() {
                fetch('/update-activity', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        userId: '<?php echo htmlspecialchars($_SESSION['user-info']['userId'] ?? '', ENT_QUOTES, 'UTF-8'); ?>'
                    })
                })
                .then(response => {
                    if (!response.ok) {
                        return response.text().then(text => { throw new Error(text); });
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Activity updated:', data);
                })
                .catch(error => {
                    console.error('Error:', error);
                });
            }

            selectElement.addEventListener('change', function(event) {
                const status = event.target.value;
                const userId = '<?php echo htmlspecialchars($_SESSION['user-info']['userId'] ?? '', ENT_QUOTES, 'UTF-8'); ?>';
                
                const formData = new FormData();
                formData.append('status', status);
                formData.append('userId', userId);
                
                let statusColor;

                switch (status) {
                    case 'online':
                        statusColor = 'green';
                        break;
                    case 'offline':
                        statusColor = 'gray';
                        break;
                    case 'absent':
                        statusColor = '#f09000';
                        break;
                }

                selectElement.style.color = statusColor;

                fetch('/update-status', {
                    method: 'POST',
                    body: formData,
                }).then(() => {
                    currentStatus = status;

                    if (status !== 'offline') {
                        startActivityUpdates();
                    } else {
                        stopActivityUpdates();
                    }
                });
            });
            let activityInterval;
            function startActivityUpdates() {
                if (!activityInterval) {
                    activityInterval = setInterval(updateActivity, 220000);
                }
            }

            function stopActivityUpdates() {
                if (activityInterval) {
                    clearInterval(activityInterval);
                    activityInterval = null;
                }
            }

            window.onload = function() {
                currentStatus = selectElement.value;

                let initialColor;
                switch (currentStatus) {
                    case 'online':
                        initialColor = 'green';
                        break;
                    case 'offline':
                        initialColor = 'gray';
                        break;
                    case 'absent':
                        initialColor = '#f09000';
                        break;
                }

                selectElement.style.color = initialColor;

                if (currentStatus !== 'offline') {
                    startActivityUpdates();
                } else {
                    stopActivityUpdates();
                }
            }

            setInterval(() => {
                if (currentStatus !== 'offline') {
                    updateStatus('offline', '<?php echo htmlspecialchars($_SESSION['user-info']['userId'] ?? '', ENT_QUOTES, 'UTF-8'); ?>');
                }
            }, 520000);
        }
    })
</script>
<header class="header-master">
    <div class="header-main">
        <div class="logo-header">
            <a href="/" class="fill-div"></a>
        </div>
        <div class="header-left">
            <div class="header-hy-1 flexo flexCom" style="height: 100%;">
                <nav>
                    <a class="flexRow flexo nav-flex1" href="#"><div class="famfamfam-silk feed"></div>RSS</a>
                </nav>
                <nav class="nav-flex1">
                    <a class="flexRow flexo nav-flex1" href="#"><div class="famfamfam-silk connect"></div>API</a>
                </nav>
            </div>
        </div>
        <div class="header-right">
            <nav>
                <?php
                    if ($_SESSION['type-user'] == 'user') {
                        echo "
                        <a class='a-stylezer2 bold-t' href='/logout'>Sair</a>
                        <a class='a-stylezer2' href='/settings'>Configurações</a>
                        ";
                    } else if ($_SESSION['type-user'] == 'guest') {
                        echo '
                        <a class="a-stylezer2 bold-t" href="/signup">Cadastrar</a>
                        <a class="a-stylezer2 bold-t" href="/login">Entrar</a>
                        ';
                    }
                ?>
                <a href="#">Idioma</a>
            </nav>
            <div class="flexoNoN font-small margin-to-right marginV" style="gap: 5px;">
                <?php
                use app\database\ConnectionRedis;
                if (isset($_SESSION['user-info']['userId'])) {
                    $userId = htmlspecialchars($_SESSION['user-info']['userId'] ?? '', ENT_QUOTES, 'UTF-8');
                    $status = ConnectionRedis::getData('users', $userId, 'status');
                    $status = is_array($status) ? implode($status) : 'offline';
                    echo "
                    <select 
                        name='status' 
                        class='font-small round-01' 
                        style='font-weight: bold;'>
                        <option value='online' style='color: green;' " . ($status === 'online' ? 'selected' : '') . ">Online</option>
                        <option value='offline' style='color: gray;' " . ($status === 'offline' ? 'selected' : '') . ">Offline</option>
                        <option value='absent' style='color: #f09000;' " . ($status === 'absent' ? 'selected' : '') . ">Ausente</option>
                    </select>
                    ";
                }
                ?>
                <div>
                    <?php 
                    if (isset($_SESSION['user-info']['user_url'])) {
                        $username = htmlspecialchars($_SESSION['user-info']['username'] ?? '', ENT_QUOTES, 'UTF-8');
                        $user_url = htmlspecialchars($_SESSION['user-info']['user_url'] ?? '', ENT_QUOTES, 'UTF-8');
                        echo "
                        <a class='bold-t text-shaded' href='/user/{$user_url}'>{$username}</a>
                        ";
                    }
                    ?>
                </div>
            </div>


        </div>
    </div>
    <div class="header-above">
        <div class="header-bovemain">
            <div class="header-left headerbove-left flexo">
                <form method="get" class="search-form">
                    <input name="content-search" class="search-bar round-d" type="text">
                    <select class="round-dd" name="type-content" id="TypeOfContent">
                        <option value="all">Tudo</option>
                        <option value="post">Postagem</option>
                        <option value="video">Vídeo</option>
                        <option value="community">Comunidade</option>
                        <option value="profile">Perfil</option>
                    </select>
                    <input class="ser-button round-a" type="submit" value="Pesquisar">
                </form>
            </div>
            <div class="header-bovecenter">
                <nav class="ile-selectrk flexoNoN">
                    <?php ?>
                    <a class="ile-a flexoNoN" href="#">Meus Videos</a>
                    <a class="ile-a flexoNoN" href=<?php
                    if (isset($_SESSION['user-info']['userId'])) {
                        $loggedIdUrl = htmlspecialchars($_SESSION['user-info']['user_url'] ?? '', ENT_QUOTES, 'UTF-8');
                        echo "/user/{$loggedIdUrl}/posts";
                    } else {
                        echo "/";
                    }
                    ?>>Minhas Postagens</a>
                    <a class="ile-a flexoNoN" href=<?php 
                    if (isset($_SESSION['user-info']['userId'])) {
                        $loggedIdUrl = htmlspecialchars($_SESSION['user-info']['user_url'] ?? '', ENT_QUOTES, 'UTF-8');
                        echo "/user/{$loggedIdUrl}";
                    } else {
                        echo "/";
                    }
                    ?>>Meu Perfil</a>
                </nav>
            </div>
            <div class="header-right flexo">
                <div class="button-wrapper-header">
                    <a style="margin-left:auto;" class="denbutton" href="/create">
                        <div class="flexoNoN" style="gap: 3px;">
                            <div class="famfamfam-silk add"></div>
                            <b>Criar</b>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
</header>