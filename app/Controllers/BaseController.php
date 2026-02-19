<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

/**
 * Class BaseController
 *
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 * Extend this class in any new controllers:
 *     class Home extends BaseController
 *
 * For security be sure to declare any new methods as protected or private.
 */
abstract class BaseController extends Controller
{
  /**
   * Instance of the main Request object.
   *
   * @var CLIRequest|IncomingRequest
   */
  protected $request;

  /**
   * An array of helpers to be loaded automatically upon
   * class instantiation. These helpers will be available
   * to all other controllers that extend BaseController.
   *
   * @var list<string>
   */
  protected $helpers = [];
  public $csss = [];
  public $jss = [];
  public $frontVersion = 19;
  public $user;
  public $usizes;
  public $esizes;
  public $meta;
  public $title;
  public $controller;
  public $db;
  public $mc_scripts;
  public $noview;
  protected $datos = [];

  /**
   * Be sure to declare properties for any property fetch you initialized.
   * The creation of dynamic property is deprecated in PHP 8.2.
   */
  // protected $session;

  /**
   * @return void
   */
  public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
  {
    $this->db = db_connect();

    $session = session();
    $this->user = (object)[
      'id' => $session->get('id'),
      'name' => $session->get('user'),
      'type' => $session->get('type'),
      'admin' => $session->get('admin'),
    ];

    $this->datos['user'] = $this->user;

    $this->controller = strtolower(class_basename(service('router')->controllerName()));
    $this->datos['controller'] = $this->controller;


    $this->title = 'wAvisos: El clasificado que vive en WhatsApp.';
    $this->meta = (object) array(
      'title' => $this->title,
      'description' => 'Publica tus anuncios de manera rápida, sencilla y gratuita. Encuentra ofertas, productos y servicios en nuestro tablero de anuncios online. ¡Es fácil y sin costo!',
      'keywords' => 'tablero de anuncios, minianuncios, anuncios gratuitos, publicar gratis, vender productos, servicios online, clasificados, ofertas locales, anuncios online',
      'image' => base_url('/sys/assets/img/tablero.jpeg'),
      'url' => current_url(),
      'site_name' => 'WAVISOS',
    );

    // Do Not Edit This Line
    parent::initController($request, $response, $logger);

    // Preload any models, libraries, etc, here.

    // E.g.: $this->session = service('session');
  }

    public function getDataConn()
  {
    return array(
      'user' => $this->db->username,
      'pass' => $this->db->password,
      'db' => $this->db->database,
      'host' => $this->db->hostname
    );
  }

  public function dieAjax()
  {
    if (
      isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
      strcasecmp($_SERVER['HTTP_X_REQUESTED_WITH'], 'xmlhttprequest') == 0
    ) {
      return true;
    }
    $this->dieMsg(false, "No es ajax.");
  }

  public function getinfo($cate = '', $subc = '', $nsubc = '')
  {
    if (!empty($cate)&&$cate!='todo') $icate = $this->db->query("SELECT * FROM categoria WHERE cate_ini='{$cate}'")->getRow();
    else $icate = (object)['cate_nombre' => 'Todo', 'cate_ini' => 'todo', 'cate_id' => ''];
    if (!empty($subc)) $isubc = $this->db->query("SELECT * FROM subcate WHERE subc_cate_id='{$icate->cate_id}' AND subc_ini='{$subc}'")->getRow();
    else $isubc = (object)['subc_id' => '', 'subc_nombre' => '', 'subc_ini' => ''];
    if (!empty($nsubc)) $isubc = $this->db->query("SELECT * FROM subcate WHERE subc_cate_id='{$icate->cate_id}' AND subc_id='{$nsubc}'")->getRow();
    return (object)['cate' => $icate, 'subc' => $isubc];
  }

  public function isAjax()
  {
    if (
      isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
      strcasecmp($_SERVER['HTTP_X_REQUESTED_WITH'], 'xmlhttprequest') == 0
    ) {
      return true;
    }
    return false;
  }

  public function addJs($jss)
  {
    if (is_array($jss)) $this->jss = $jss;
    else $this->jss[] = $jss;
  }
  public function addCss($csss)
  {
    if (is_array($csss)) $this->csss = $csss;
    else $this->csss[] = $csss;
  }

  public function showContent($path, $response = [])
  {

    $router = service('router');
    $controller  = preg_replace("#.App.Controllers.#", '', $router->controllerName());

    echo view(str_replace("\\", "/", strtolower($controller)) . '/' . $path, array_merge($this->datos, $response));
  }



  public function showHeader()
  {
    $strcss = '';

    foreach ($this->csss as $css) {
      $strcss .= '<link href="' . ((preg_match('#^htt#', $css) == TRUE) ? '' : base_url('sys/assets') . '/') . $css . '?v=' . $this->frontVersion . '" rel="stylesheet" type="text/css" media="all" />';
    }

    $this->datos['css'] = $strcss;

    if ($this->title != $this->meta->title) $this->meta->title = $this->meta->title . ' | ' . $this->meta->site_name;
    $this->datos['meta'] = $this->meta;
    echo view('templates/header', $this->datos);
  }

  public function showFooter()
  {
    $strjs = '';

    foreach ($this->jss as $js) {
      $strjs .= '<script type="text/javascript" src="' . ((preg_match('#^htt#', $js) == TRUE) ? '' : base_url('sys/assets') . '/') . $js . '?v=' . $this->frontVersion . '"></script>';
    }

    helper('formulario');

    $datos['js'] = $strjs;
    echo view('templates/footer', $datos);
  }

  public function dieMsg($ret = true, $msg = "", $redirect = "", $data = [])
  {
    if ($ret == false) {
      $this->response->setStatusCode(500, $msg);
      $this->response->send();
      exit(0);
    }
    $resp = ['exito' => $ret, 'mensaje' => $msg, 'redirect' => $redirect, 'data' => $data];
    $this->response->setJSON($resp);
    $this->response->send();
    exit(0);
  }
}
