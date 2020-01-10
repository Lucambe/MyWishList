<?php

namespace mywishlist\controllers;

use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use mywishlist\models\User;
use Slim\Http\Request;
use Slim\Http\Response;

class AuthController extends Controller {

    public function login(Request $request, Response $response, array $args): Response {
        try {
            $login = filter_var($request->getParsedBodyParam('id'), FILTER_SANITIZE_STRING);
            $password = filter_var($request->getParsedBodyParam('password'), FILTER_SANITIZE_STRING);

            $user = User::where('pseudo', '=', $login)->orWhere('email', '=', $login)->firstOrFail();
            if(!password_verify($password, $user->password)) throw new Exception('Votre mot de passe est incorrect');

            $_SESSION['user'] = $user;

            $response = $response->withRedirect($this->router->pathFor('showAccount'));
        }  catch (ModelNotFoundException $e) {
            $this->flash->addMessage('error', 'Aucun compte associé à cet identifiant n\'a été trouvé.');
            $response = $response->withRedirect($this->router->pathFor('showLogin'));
        } catch (Exception $e) {
            $this->flash->addMessage('error', $e->getMessage());
            $response = $response->withRedirect($this->router->pathFor('showLogin'));
        }
        return $response;
    }

    public function register(Request $request, Response $response, array $args): Response {
        try {
            $pseudo = filter_var($request->getParsedBodyParam('pseudo'), FILTER_SANITIZE_STRING);
            $email = filter_var($request->getParsedBodyParam('email'), FILTER_SANITIZE_EMAIL);
            $password = filter_var($request->getParsedBodyParam('password'), FILTER_SANITIZE_STRING);
            $password_conf = filter_var($request->getParsedBodyParam('password_conf'), FILTER_SANITIZE_STRING);

            if (mb_strlen($pseudo, 'utf8') < 2 || mb_strlen($pseudo, 'utf8') > 35) throw new Exception("Votre pseudo doit contenir entre 2 et 35 caractères.");
            if (User::where('pseudo', '=', $pseudo)->exists()) throw new Exception("Ce pseudo est déjà pris.");
            if (User::where('email', '=', $email)->exists()) throw new Exception("Cet email est déjà utilisée.");
            if ($password != $password_conf) throw new Exception("La confirmation du mot de passe n'est pas bonne");
            if (mb_strlen($password, 'utf8') < 8) throw new Exception("Votre mot de passe doit contenir au moins 8 caractères");

            $user = new User();
            $user->pseudo = $pseudo;
            $user->email = $email;
            $user->password = password_hash($password_conf, PASSWORD_DEFAULT);
            $user->save();


            $this->flash->addMessage('success', "$pseudo, votre compte a été créé! Vous pouvez dès à présent vous connecter.");
            $response = $response->withRedirect($this->router->pathFor('showLogin'));
        } catch (Exception $e) {
            $this->flash->addMessage('error', $e->getMessage());
            $response = $response->withRedirect($this->router->pathFor('showRegister'));
        }
        return $response;
    }

    public function logout(Request $request, Response $response, array $args): Response {
        unset($_SESSION['user']);
        $this->flash->addMessage('success', 'Vous avez été deconnecté');
        return $response->withRedirect($this->router->pathFor('home'));
    }

    public function delete(Request $request, Response $response, array $args): Response {
        return $response;
    }

}