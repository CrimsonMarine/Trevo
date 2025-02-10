<?php $this->layout('master', ['title' => $title])?>

<div class="flexo prof-container" style="gap: 10px;">
    <div class="margin-to-left font-left sub-p4">
        <div>
            <p class="bold-t marginV-none font-large">Postagens</PO>
            <?php $this->insert('layouts/elements/posthPosts')?>
            <?php $this->insert('layouts/elements/mostrecPosts')?>
        </div>
    </div>
</div>
