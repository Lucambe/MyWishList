<form id="form_message" method="POST" action="<?= $router->pathFor('inscription') ?>">
    <div class="modal-body">
        <div class="form-group">
            <label for="inputNick">Entrez un pseudo pseudo</label>
            <input type="text" name="nick" class="form-control" id="inputNick" placeholder="Xavier">
        </div>
        <!-- Verif si déjà dans la BDD et affichage d'un message en rouge si pas bon sinon petit check vert-->
        <div class="form-group">
            <label for="inputPassword">Entrez votre mot de passe</label>
            <input type="text" name="password" class="form-control" id="inputPassword">
        </div>
        <div class="form-group">
            <label for="inputPassword">Entrez le même mot de passe</label>
            <input type="text" name="password2" class="form-control" id="inputPassword2">
        </div>
        <!-- Verif si mdps pareils et affichage d'un message en rouge si pas bon sinon petit check vert-->
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Retour</button>
        <button type="submit" class="btn btn-primary">Envoyer</button>
    </div>
</form>