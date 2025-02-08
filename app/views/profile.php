<?php $this->layout('master', ['title' => $title, 'username' => $username, 'status' => $status, 'lastActivity' => $lastActivity, 'currentUserId' => $currentUserId, 'following' => $following, 'user' => $user, 'posts' => $posts])?>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const followButton = document.getElementById('followButton');
        const FollowButton = document.getElementById('FollowButton');

        if (document.body.contains(FollowButton)) {
            const userId = followButton.dataset.userId;
            
            if (userId != "<?= isset($_SESSION['user-info']['userId'])?>") {
                checkFollowStatus(userId);
            }

            FollowButton.addEventListener('click', () => handleFollowToggle(userId));
        }
    });

    async function checkFollowStatus(userId) {
        const response = await fetch(`/api/follow-status/${userId}`);
        const data = await response.json();

        updateFollowButton(data.following);
    }

    async function handleFollowToggle(userId) {
        try {
            const response = await fetch(`/api/toggle-follow/${userId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
            });

            if (response.redirected) {
                window.location.href = response.url;
                return;
            }

            const contentType = response.headers.get("content-type");
            if (!contentType || !contentType.includes("application/json")) {
                throw new Error("Resposta inválida do servidor");
            }

            const data = await response.json();

            if (data.error) {
                window.location.href = '/login';
                return;
            }

            updateFollowButton(data.following);

        } catch (error) {
            console.error('Erro ao alternar o status de seguir:', error);
        }
    }

    function updateFollowButton(isFollowing) {
        const followButton = document.getElementById('followButton');
        const actFollow = document.getElementById('actFollow');
        if (!followButton) return;
        if (!actFollow) return;

        followButton.textContent = isFollowing ? 'Deixar de seguir' : 'Seguir';
        actFollow.classList.remove('gminus', 'gplus');
        actFollow.classList.add(isFollowing ? 'gminus' : 'gplus');

    }
</script>
<style>
    .sub-p1 {
        width: 65%;
        flex-grow: 4;
        height: fit-content;
    }

    .sub-p2 {
        width: 35%;
        flex-grow: 1;
    }

    .prof-container {
        display: flex;
        flex-direction: row;
        width: 100%;
    }

    #container {
        text-shadow: none !important;
    }

    .pear-box-2-prf {
        font-family: Arial, Helvetica, sans-serif;
        width: fit-content;
        border: 1px solid <?= $userCustomization['pear_elementColor1']?>;
        box-sizing: border-box;
        background: linear-gradient(to bottom, <?= $userCustomization['pear_elementColor2']?>, <?= $userCustomization['pear_elementColor1']?>);
        box-shadow: 
            0 1px 3px rgba(0,0,0,0.1);
        padding: 10px;
        overflow: hidden;
    }

    .mark-1-prf {
        background: linear-gradient(to top, rgba(177, 177, 177, 0), <?= $userCustomization['pear_elementColor2']?>);
        border-radius: 3px;
        padding: 5px;
    }

    
    @media all and (max-width:910px) {
        .prof-container {
            flex-direction: column; 
        }
        .pear-box-2-prf {
            width: 100%;
        }
        .sub-p1, .sub-p2 {
            width: 100%;
        }
    }
</style>

<div class='flexo prof-container' style='gap: 10px;'>
    <div class='font-left pear-box-2-prf sub-p1'>
        <div class='back-1'>
            <div class='ins-padd1 flexo flexCom'>
                <div class='flexCom' style='gap: 5px;'>
                    <div class='flexoNoN flexRow' style='height:100%;'>
                        <b class='font-large' style="color: <?= $userCustomization['usern_color']?>"><?php echo $this->e($username)?></b>
                        <div class='margin-to-right'>
                        <?php
                            if (isset($currentUserId)) {
                                if ($currentUserId != $userId) {
                                    echo "
                                        <div class='flw-button-wrapper flexo flexRow' id='FollowButton' style='height:23px; width: 100%;'>
                                            <button class='follow-button' id='followButton' data-user-id='{$user['id']}'>
                                                " . ($following ? 'Deixar de seguir' : 'Seguir') . "
                                            </button>
                                            <div class='flwStatus'>
                                                <div id='actFollow' class=" . ($following ? 'gminus' : 'gplus') . "></div>
                                            </div>
                                        </div>
                                    ";
                                }
                            }
                        ?>
                        </div>
                    </div>
                    <?php 
                    $color;
                    $IconDegree;
                    switch ($status) {
                        case 'online':
                            $color = 'green';
                            $status = 'Online';
                            $IconDegree = 'hue-rotate(-10deg) brightness(150%)';
                            break;
                        case 'offline':
                            $color = 'gray';
                            $status = 'Offline';
                            $IconDegree = 'saturate(0%) brightness(150%)';
                            break;
                        case 'absent':
                            $color = '#f09000';
                            $status = 'Ausente';
                            $IconDegree = 'hue-rotate(260deg) brightness(150%)';
                            break;
                    }
                    ?>
                    <div >
                        <div class='flexoNoN' style='padding: 5px 5px 5px 0; height: 50%; gap: 5px;'>
                            <div class='little-box1 flexoNoN flexRow'>
                                <div class='StatusIcon flexoNoN' style='filter: <?php echo $IconDegree?>;'></div><b style='color: <?php echo $color?>;'><?php echo $this->e($status)?></b>
                                <em class="LastAc-InProfile">Ultima Atividade: <?php echo $this->e($lastActivity)?></em>
                            </div>
                        </div>
                    </div>
                    <hr>
                </div>
            </div>
        </div>
    </div>
    <div class='flexCom flexo font-left pear-box-2-prf sub-p2'>
        <div class='back-1'>
            <div class='ins-padd1'>
                <div class='flexRow flexoNoN mark-1-prf'>
                    <b class='font-larger'>Postagens</b>
                    <span class='font-large margin-to-right'><a href='/user/<?php echo $userUrl?>/posts'><span>(<?php echo count($posts)?>)</span></a></span>
                </div>
                <?php if (!empty($posts)): ?>
                <?php foreach ($posts as $post): ?>
                    <div class='post-container1' onclick="window.location.href='/post/<?= htmlspecialchars($post['post_url']) ?>'">
                        <div class='postProfile flexo flexCom'>
                            <b class='font-large'><?php echo $this->e($post['title']) ?></b>
                            <p><?php echo $this->e($post['content']) ?></p>
                            <div class='flexRow flexoNoN' style='margin: 5px 0 5px 0'>
                                <b>Publicado em: <?php echo $this->e(date('Y/m/d', strtotime($post['date']))) ?></b>
                                <?php $this->insert('layouts/otherPostsElements1', ['post' => $post])?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p style='font-weight: bold;'>Esse usuário não possue postagens.</p>
            <?php endif; ?>
            </div>
        </div>
    </div>
</div>
