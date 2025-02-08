<form method="post" id="formPost" class="flexo flexCom">
    <script src="/assets/js/verifyString.js"></script>
    <label class="tLabel1" for="post-title">Título:</label>
    <input id="inputTo50" type="text" name="post-title" class="round-b">
    <b><span id="textTo50"></span>/50</b>
    <br>
    <label class="tLabel1" for="post-content">Texto:</label>
    <textarea class="textarea1 round-b" id="inputTo200" name="post-content" required></textarea>
    <b><span id="textTo200"></span>/200</b>
    <br>
    <input type="hidden" name="form-type" value="postagem">
    <input class="ser-button round-b" type="submit" value="Publicar">
</form>