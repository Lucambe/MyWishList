<?php

namespace mywishlist\controllers;

use BadMethodCallException;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use mywishlist\models\User;
use mywishlist\models\Liste;
use mywishlist\models\Item;
use mywishlist\models\Reservation;
use mywishlist\models\Message;
use mywishlist\models\Participe;
use mywishlist\models\Cagnotte;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class AuthController
 * @author Jules Sayer <jules.sayer@protonmail.com>
 * @package mywishlist\controllers
 */
class AuthController extends Controller {

    /**
     * Connecte l'utilisateur
     *
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response
     */
    public function login(Request $request, Response $response, array $args): Response {
        try {
            if (isset($_SESSION['user'])) throw new BadMethodCallException("Vous êtes déjà connecté");
            $login = filter_var($request->getParsedBodyParam('id'), FILTER_SANITIZE_STRING);
            $password = filter_var($request->getParsedBodyParam('password'), FILTER_SANITIZE_STRING);

            $user = User::where('pseudo', '=', $login)->orWhere('email', '=', $login)->firstOrFail();
            if(!password_verify($password, $user->password)) throw new Exception('Votre mot de passe est incorrect');

            $_SESSION['user'] = $user;

            $response = $response->withRedirect($this->router->pathFor('showAccount'));
        } catch (BadMethodCallException $e) {
            $this->flash->addMessage('error', $e->getMessage());
            $response = $response->withRedirect($this->router->pathFor('home'));
        } catch (ModelNotFoundException $e) {
            $this->flash->addMessage('error', 'Aucun compte associé à cet identifiant n\'a été trouvé.');
            $response = $response->withRedirect($this->router->pathFor('showLogin'));
        } catch (Exception $e) {
            $this->flash->addMessage('error', $e->getMessage());
            $response = $response->withRedirect($this->router->pathFor('showLogin'));
        }
        return $response;
    }

    /**
     * Inscrit l'utilisateur
     *
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response
     */
    public function register(Request $request, Response $response, array $args): Response {
        try {
            if (isset($_SESSION['user'])) throw new BadMethodCallException("Vous êtes déjà connecté");
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
        } catch (BadMethodCallException $e) {
            $this->flash->addMessage('error', $e->getMessage());
            $response = $response->withRedirect($this->router->pathFor('home'));
        } catch (Exception $e) {
            $this->flash->addMessage('error', $e->getMessage());
            $response = $response->withRedirect($this->router->pathFor('showRegister'));
        }
        return $response;
    }

    /**
     * Déconnecte l'utilisateur
     *
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response
     */
    public function logout(Request $request, Response $response, array $args): Response {
        unset($_SESSION['user']);
        $this->flash->addMessage('success', 'Vous avez été deconnecté');
        return $response->withRedirect($this->router->pathFor('home'));
    }

    /**
     * Supprime le compte et
     * les données associées
     *
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response
     */
    public function delete(Request $request, Response $response, array $args): Response {
        return $response;
    }

    /**
     * Permet de mettre à
     * jour le compte
     *
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response
     */
    public function updateAccount(Request $request, Response $response, array $args): Response {
        try {
            if(!isset($_SESSION['user'])) throw new BadMethodCallException("Vous devez être connecté pour faire ça");
            $email = filter_var($request->getParsedBodyParam('email'), FILTER_SANITIZE_EMAIL);
            $password = filter_var($request->getParsedBodyParam('password'), FILTER_SANITIZE_STRING);

            if (!password_verify($password, $_SESSION['user']->password)) throw new Exception('Votre mot de passe est incorrect');
            if ($email != $_SESSION['user']->email) {
                if (User::where('email', '=', $email)->exists()) throw new Exception("Cet email est déjà utilisée.");
            }

            $_SESSION['user']->email = $email;
            $_SESSION['user']->save();

            $this->flash->addMessage('success', "Votre compte a été modifié");
            $response = $response->withRedirect($this->router->pathFor('showAccount'));
        } catch (BadMethodCallException $e) {
            $this->flash->addMessage('error', $e->getMessage());
            $response = $response->withRedirect($this->router->pathFor('home'));
        } catch (Exception $e) {
            $this->flash->addMessage('error', $e->getMessage());
            $response = $response->withRedirect($this->router->pathFor('showAccount'));
        }
        return $response;
    }

    /**
     * Permet de mettre à jour
     * le mot de passe
     *
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response
     */
    public function updatePassword(Request $request, Response $response, array $args): Response {
        try {
            if(!isset($_SESSION['user'])) throw new BadMethodCallException("Vous devez être connecté pour faire ça");
            $password = filter_var($request->getParsedBodyParam('password'), FILTER_SANITIZE_STRING);
            $new_password = filter_var($request->getParsedBodyParam('new_password'), FILTER_SANITIZE_STRING);
            $new_password_conf = filter_var($request->getParsedBodyParam('new_password_conf'), FILTER_SANITIZE_STRING);

            if (!password_verify($password, $_SESSION['user']->password)) throw new Exception('Votre mot de passe est incorrect');
            if ($new_password != $new_password_conf) throw new Exception("La confirmation du mot de passe n'est pas bonne");
            if (mb_strlen($password, 'utf8') < 8) throw new Exception("Votre mot de passe doit contenir au moins 8 caractères");

            $_SESSION['user']->password = password_hash($new_password_conf, PASSWORD_DEFAULT);
            $_SESSION['user']->save();

            $this->flash->addMessage('success', "Votre mot de passe a été modifié, vous avez été déconnecté");
            $response = $response->withRedirect($this->router->pathFor('logout'));
        } catch (BadMethodCallException $e) {
            $this->flash->addMessage('error', $e->getMessage());
            $response = $response->withRedirect($this->router->pathFor('home'));
        } catch (Exception $e) {
            $this->flash->addMessage('error', $e->getMessage());
            $response = $response->withRedirect($this->router->pathFor('showAccount'));
        }
        return $response;
    }

    public function deleteAccount(Request $request, Response $response, array $args): Response {
        try{
            if(!isset($_SESSION['user'])) throw new BadMethodCallException("Vous devez être connecté pour faire ça.");

            $user = User::where('id','=',$_SESSION['user']->id)->first();
            $liste = Liste::where('user_id','=',$user->id)->first();
            $item = Item::where('liste_id','=',$liste->no)->get();
            $message = Message::where('idListe','=',$liste->no)->get();
            $reserv = Reservation::where('liste_id','=',$liste->no)->get();
            $participe = Participe::where('id_user','=', $user->id)->first();
            $cagnotte = Cagnotte::where('id','=', $participe->id_cagnotte)->first();

            if($user->exists()) $user->delete();
            session_destroy();

            /*
            if($cagnotte->exists())
             $cagnotte->delete();
            if($item->exists()) 
             $item->delete();
            if($liste->exists())
             $liste->delete();
            if($participe->exists())
             $participe->delete();
            if($message->exists())
             $message->delete();
            if($reserv->exists())
             $reserv->delete();
            */

            $this->flash->addMessage('success', "Votre compte personnel a été supprimé avec succès.");
            $response = $response->withRedirect($this->router->pathFor('home'));
        }catch (BadMethodCallException $e) {
            $this->flash->addMessage('error', $e->getMessage());
            $response = $response->withRedirect($this->router->pathFor('showAccount'));
        } catch (Exception $e) {
            $this->flash->addMessage('error', $e->getMessage());
            $response = $response->withRedirect($this->router->pathFor('showAccount'));
        }
        return $response;
    }

}