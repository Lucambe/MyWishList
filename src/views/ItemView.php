<?php
namespace mywishlist\views;
class ItemView {

    private $model;
    private $select;

    public function __construct($model, $s = LIST_VIEW) {
        $this->model = $model;
        $this->select = $s;
    }

    public function render() {
        switch ($this->select) {
            case "LIST_VIEW" : {
                $content = $this->htmlListAllItems();
                break;
            }
            case "ITEM_VIEW" : {
                $content = $this->htmlOneItem();
                break;
            }
            case "ITEM_404" : {
                $content = $this->htmlItemDoesNotExist();
                break;
            }
            default : {
                $content = $this->htmlListAllItems();
                break;
            }
        }
        echo $content;
    }

    private function htmlOneItem() {
        $html = <<<END
<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <link rel="stylesheet" href="../public/css/bootstrap.css">
        <title>{$this->model->nom}</title>
    </head>
    <body>
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark static-top">
            <div class="container">
                <a class="navbar-brand" href="#">MyWishList</a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarResponsive">
                    <ul class="navbar-nav ml-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="/">Accueil
                                <span class="sr-only">(current)</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">A propos</a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Espace membres</a>
                            <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                                <a class="dropdown-item" href="#">Connexion</a>
                                <a class="dropdown-item" href="#">Inscription</a>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <div class="container">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">n° Liste</th>
                            <th scope="col">Nom</th>
                            <th scope="col">Description</th>
                            <th scope="col">Image</th>
                            <th scope="col">Tarif</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <th scope="row">{$this->model->id}</th>
                            <td>{$this->model->liste_id}</td>
                            <td>{$this->model->nom}</td>
                            <td>{$this->model->descr}</td>
                            <td><img src="../public/images/{$this->model->img}" class="img-fluid img-thumbnail"></td>
                            <td>{$this->model->tarif}</td>
                        </tr>
                    </tbody>
                </table>
        </div>

        <script src="../public/js/jquery.slim.min.js"></script>
        <script src="../public/js/bootstrap.min.js"></script>
    </body>
</html>
END;
        return $html;
    }

    private function htmlItemDoesNotExist() {
        $html = <<<END
<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <link rel="stylesheet" href="../public/css/bootstrap.css">
        <title>Item inexistant</title>
    </head>
    <body>
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark static-top">
            <div class="container">
                <a class="navbar-brand" href="#">MyWishList</a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarResponsive">
                    <ul class="navbar-nav ml-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="/">Accueil
                                <span class="sr-only">(current)</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">A propos</a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Espace membres</a>
                            <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                                <a class="dropdown-item" href="#">Connexion</a>
                                <a class="dropdown-item" href="#">Inscription</a>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <div class="container">
            <div class="row">
                <div class="col-lg-12 text-center">
                    <h1 class="mt-5">Item inexistant</h1>
                </div>
            </div>
        </div>

        <script src="../public/js/jquery.slim.min.js"></script>
        <script src="../public/js/bootstrap.min.js"></script>
    </body>
</html>
END;
        return $html;
    }

    private function htmlListAllItems() {
        $html = <<<END
<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <link rel="stylesheet" href="public/css/bootstrap.css">
        <title>Listes des items</title>
    </head>
    <body>
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark static-top">
            <div class="container">
                <a class="navbar-brand" href="#">MyWishList</a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarResponsive">
                    <ul class="navbar-nav ml-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="/">Accueil
                                <span class="sr-only">(current)</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">A propos</a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Espace membres</a>
                            <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                                <a class="dropdown-item" href="#">Connexion</a>
                                <a class="dropdown-item" href="#">Inscription</a>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <div class="container">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">n° Liste</th>
                            <th scope="col">Nom</th>
                            <th scope="col">Description</th>
                            <th scope="col">Image</th>
                            <th scope="col">Tarif</th>
                        </tr>
                    </thead>
                    <tbody>
END;
foreach($this->model as $item) {
    $html .= <<<END

                        <tr>
                            <th scope="row">{$item->id}</th>
                            <td>{$item->liste_id}</td>
                            <td>{$item->nom}</td>
                            <td>{$item->descr}</td>
                            <td><img src="public/images/{$item->img}" class="img-fluid img-thumbnail"></td>
                            <td>{$item->tarif}</td>
                        </tr>
END;
}
$html .= <<<END
                    </tbody>
                </table>
        </div>

        <script src="public/js/jquery.slim.min.js"></script>
        <script src="public/js/bootstrap.min.js"></script>
    </body>
</html>
END;
    return $html;
    }
}