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
        return $content;
    }

    private function htmlOneItem() {
        $rootUri = \Slim\Slim::getInstance()->request->getRootUri();
        $html = <<<END
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
            <td><img src="{$rootUri}/public/images/{$this->model->img}" class="img-fluid img-thumbnail"></td>
            <td>{$this->model->tarif}</td>
        </tr>
    </tbody>
</table>
END;
        return $html;
    }

    private function htmlItemDoesNotExist() {
        $html = <<<END
<div class="row">
    <div class="col-lg-12 text-center">
        <h1 class="mt-5">Item inexistant</h1>
    </div>
</div>
END;
        return $html;
    }

    private function htmlListAllItems() {
        $rootUri = \Slim\Slim::getInstance()->request->getRootUri();
        $html = <<<END
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
            <td><img src="{$rootUri}/public/images/{$item->img}" class="img-fluid img-thumbnail"></td>
            <td>{$item->tarif}</td>
        </tr>
END;
}
$html .= <<<END
    </tbody>
</table>
END;
        return $html;
    }
}