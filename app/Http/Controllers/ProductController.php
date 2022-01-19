<?php

namespace Http\Controllers;

use Database\DBAccess;
use Http\Controllers\Controller;

class ProductController extends Controller
{
  /**
   * GET products
   *
   * @return void
   */
  public function get()
  {
    /**
     * 検索した商品を表示する画面
     * $products → fetchAll()
     */
    // もしURLパラメータがあれば、取得と検証
    if (isset($_GET['search'])) {
      $search = $_GET['search'];
    } else {
      $search = "";
    }
    // データベースから、商品の一覧を取得する
    $dba = DBAccess::getInstance();
    $stmt = $dba->query("SELECT distinct products.id,name,price,status,path FROM products LEFT OUTER JOIN pictures ON products.id = pictures.product_id WHERE name LIKE ? || description LIKE ? LIMIT 30;", ["%$search%", "%$search%"]);

    $products = $stmt->fetchAll();

    $params = ['products' => $products];
    $this->view("products/list.php", $params);
  }

  /**
   * GET products/[:id]
   *
   * @param [int] $id
   * @return void
   */
  public function fetchByID($id)
  {
    // 値の検証

    // データベースから商品情報と商品画像を取得
    $dba = DBAccess::getInstance();
    $stmt = $dba->query("SELECT * FROM products WHERE id = ? LIMIT 1;", [$id]);
    $product = $stmt->fetch();

    $stmt = $dba->query("SELECT * FROM pictures WHERE product_id = ?;", [$id]);
    $pictures = $stmt->fetchAll();

    $user = $this->auth->getUser();
    $user_id = $user->getId();
    $is_seller = $product['user_id'] === $user_id;

    // TODO: 写真のpathを絶対パスに変更
    $params = ['id' => $id, 'product' => $product, 'pictures' => $pictures, 'is_seller' => $is_seller];
    $this->view("products/index.php", $params);
  }

  /**
   * GET products/[:id]/update
   *
   * @return void
   */
  public function renderUpdate($id)
  {
    echo "ID: ${id}の商品の更新";
    // ログイン確認

    /**
     * 本人確認
     * 
     * 出品者とログインしている人が同一か?
     */

    $this->view("products/update.php");
  }

  /**
   * POST products/[:id]/update
   *
   * @return void
   */
  public function update($id)
  {
    echo "ID: ${id}の商品の出品更新";
    // ログイン確認

    /**
     * 本人確認
     * 
     * 出品者とログインしている人が同一か?
     */

    // データベースに上書きする

    $this->view("products/${id}");
  }
}
