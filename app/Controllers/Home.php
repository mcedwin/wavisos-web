<?php

namespace App\Controllers;

use App\Models\AnuncioModel;
use App\Services\GeoService;
use App\Models\CiudadModel;
use App\Models\UsuariosModel;

class Home extends BaseController
{
  protected $model;
  public function __construct()
  {
    $this->model = new AnuncioModel();
  }

  public function index()
  {
    $session = session();
    $phone = $this->request->getGet('phone');
die($phone);
    session()->set('user_phone', $phone);

    // Si ya detectamos antes
    if ($session->has('location')) {
      return redirect()->to($session->get('location_route'));
    }

    $ip = $this->request->getIPAddress();

    $location = GeoService::detectLocation($ip);

    if (!$location) {
      return view('home');
    }

    $ciudadModel = new CiudadModel();

    // Buscar ciudad en base de datos
    $ciudad = $ciudadModel
      ->select('ciudades.slug as ciudad_slug, regiones.slug as region_slug, paises.codigo_iso,ciudades.pais_id,ciudades.region_id, ciudades.id as ciudad_id')
      ->join('regiones', 'regiones.id = ciudades.region_id')
      ->join('paises', 'paises.id = regiones.pais_id')
      ->where('ciudades.nombre', $location['city'])
      ->first();

    if ($ciudad) {

      $data = [
        'pais_id'   => $ciudad['pais_id'],
        'region_id' => $ciudad['region_id'],
        'ciudad_id' => $ciudad['ciudad_id'],
      ];


      $usuamodel = new UsuariosModel();
      // 3. Actualizar donde el teléfono coincida
      $usuamodel->where('telefono', $phone)->set($data)->update();
      

      $route = '/' .
        strtolower($ciudad['codigo_iso']) . '/' .
        $ciudad['region_slug'] . '/' .
        $ciudad['ciudad_slug'];

      $session->set('location', $location);
      $session->set('location_route', $route);

      return redirect()->to($route);
    }

    return view('home');
  }

  public function cambiar($pais, $region, $ciudadId)
  {
    $ciudadModel = new CiudadModel();

    $data = $ciudadModel
      ->select('
            paises.codigo_iso,
            regiones.slug AS region_slug,
            ciudades.slug AS ciudad_slug,
            ciudades.pais_id,ciudades.region_id, ciudades.id as ciudad_id
        ')
      ->join('regiones', 'regiones.id = ciudades.region_id')
      ->join('paises', 'paises.id = regiones.pais_id')
      ->where('ciudades.id', $ciudadId)
      ->first();

    if (!$data) {
      throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
    }

    $url = '/' .
      strtolower($data['codigo_iso']) . '/' .
      $data['region_slug'] . '/' .
      $data['ciudad_slug'];

    $datau = [
      'pais_id'   => $data['pais_id'],
      'region_id' => $data['region_id'],
      'ciudad_id' => $data['ciudad_id'],
    ];


    $usuamodel = new UsuariosModel();
    // 3. Actualizar donde el teléfono coincida
    $phone = session('user_phone');
    if(!empty($phone)) $usuamodel->where('telefono', $phone)->set($data)->update();

    return redirect()->to($url);
  }

  public function porCiudad($pais, $region, $ciudad)
  {
    return view('listado', [
      'pais' => $pais,
      'region' => $region,
      'ciudad' => $ciudad
    ]);
  }

  public function nav($paisSlug, $regionSlug, $ciudadSlug, $cate = '')
  {
    $this->addJs(['js/home.js']);

    helper('formulario');
    $paisModel   = new \App\Models\PaisModel();
    $regionModel = new \App\Models\RegionModel();
    $ciudadModel = new \App\Models\CiudadModel();


    $pais = $paisModel->getByISO($paisSlug);
    $region = $regionModel->getBySlug($pais['id'], $regionSlug);
    $ciudad = $ciudadModel->getBySlug($region['id'], $ciudadSlug);

    $pais_id = $pais['id'];
    $region_id = $region['id'];
    $ciudad_id = $ciudad['id'];

    $datos['pais'] = $pais['nombre'];
    $datos['region'] = $region['nombre'];
    $datos['ciudad'] = $ciudad['nombre'];
    $phone = session('user_phone');
    $datos['telefono']  = $phone;
    $datos['pais_id'] = $pais_id;
    $datos['region_id'] = $region_id;
    $datos['ciudad_id'] = $ciudad_id;

    $paises = $paisModel->orderBy('nombre', 'ASC')->findAll();

    // 2️⃣ Cargar regiones del país seleccionado
    $regiones = $regionModel
      ->where('pais_id', $pais_id)
      ->orderBy('nombre', 'ASC')
      ->findAll();

    // Si no hay región seleccionada, usar la primera
    if (!$region_id && !empty($regiones)) {
      $region_id = $regiones[0]['id'];
    }

    // 3️⃣ Cargar ciudades de la región seleccionada
    $ciudades = [];

    if ($region_id) {
      $ciudades = $ciudadModel
        ->where('region_id', $region_id)
        ->orderBy('nombre', 'ASC')
        ->findAll();
    }

    // Si no hay ciudad seleccionada, usar la primera
    if (!$ciudad_id && !empty($ciudades)) {
      $ciudad_id = $ciudades[0]['id'];
    }

    $datos['paises'] = $paises;
    $datos['regiones'] = $regiones;
    $datos['ciudades'] = $ciudades;


    session()->set('ubicacion', [
      'pais_id' => $pais_id,
      'region_id' => $region_id,
      'ciudad_id' => $ciudad_id
    ]);



    $datos['s'] = $search = isset($_GET['s']) ? trim($_GET['s']) : '';

    $datos['info'] = $info = $this->getinfo($cate);

    $url = trim($info->cate->cate_ini, '/');

    // if (!empty($cate)) $datos['titulo'] = $this->meta->title = "Anuncios > " . $info->cate->cate_nombre;
    // else $datos['titulo'] = $this->meta->title = "Anuncios";
    $this->meta->title = $this->meta->title . (isset($_GET['page']) ? ' : Página ' . $_GET['page'] : '');

    $condiciones = [];

    // if (!empty($cate) && $cate != 'todo') $condiciones[] = "categoria_id={$info->cate->cate_id}";
    $condiciones[] = "pais_id='{$pais_id}' AND ciudad_id='{$ciudad_id}'";
    if (!empty($search))  $condiciones[] = GetQS($search, ['titulo', 'descripcion']);

    $where = count($condiciones) > 0 ? implode(' AND ', $condiciones) : "";
    if (!empty($where)) $this->model->where($where);
    $this->model->orderBy('id', 'DESC');
    $registros = $this->model->paginate(9, 'grupo');

    $datos['url'] = base_url($url);
    $datos['registros'] = $registros;

    $pager = $this->model->pager;
    $pager->setPath($url . '/' . $cate);
    $datos['pager'] = $pager;

    $this->showHeader(true);
    $this->showContent('nav', $datos);
    $this->showFooter();
  }

  // public function index()
  // {
  //   $this->addJs(['js/home.js']);
  //   helper('funciones');
  //   $datos['anuncios'] = $this->db->query("SELECT * FROM anuncios /*WHERE now()<=fechafin*/ order by fechapub desc")->getResult();

  //   $this->showHeader();
  //   $this->ShowContent('index', $datos);
  //   $this->showFooter();
  // }
  public function enviarAGPT(string $apiKey, array $mensajes): ?string
  {
    $url = "https://api.openai.com/v1/chat/completions";

    $data = [
      'model' => 'gpt-3.5-turbo',
      'messages' => $mensajes,
      'temperature' => 0.7
    ];

    $headers = [
      'Content-Type: application/json',
      'Authorization: ' . 'Bearer ' . $apiKey
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $result = curl_exec($ch);

    if (curl_errno($ch)) {
      log_message('error', 'Error en cURL: ' . curl_error($ch));
      curl_close($ch);
      return null;
    }

    curl_close($ch);

    $json = json_decode($result, true);

    return $json['choices'][0]['message']['content'] ?? null;
  }

  function getcate($text)
  {


    $conversacion = [
      ['role' => 'system', 'content' => 'Eres un asistente que clasifica mini anuncios en categorías y pone un titulo pequeño sugerente. Tu tarea es devolver solo la categoría la más adecuada para el anuncio dado, Si no encaja, clasifícalo como "Otro".
Devuelve en formato JSON el titulo sugerente y un valor numerico de la categoria.
Lista de categorías:
1. Comercios y Tiendas
2. Comida y Delivery
3. Servicios Técnicos
4. Salud y Bienestar
5. Servicios Profesionales
6. Alquileres y Bienes Raíces
7. Eventos y Celebraciones
8. Transporte
9. Instituciones Públicas y Servicios Locales']
    ];

    $prompt = $text;


    // Agregar mensaje actual del usuario
    $conversacion[] = ['role' => 'user', 'content' => $prompt];


    $apiKey = getenv('OPENAI_API_KEY'); // ponlo en .env
    $response = $this->enviarAGPT($apiKey, $conversacion);


    file_put_contents("migpt.txt", $response);
    return json_decode($response);
  }




  public function ver($id)
  {
    helper('formulario');

    $this->model->select('anuncios.id, titulo, descripcion, precio, fecha_publicacion')->join('categorias', "categoria_id=categorias.id AND anuncios.id='{$id}'");
    $datos['row'] = $row = (object)$this->model->first();

    $this->meta->title = $row->titulo;
    $this->meta->description = $row->descripcion;

    $this->db->query("UPDATE anuncios SET vistas=vistas+1 WHERE id={$id}");

    $this->showHeader(true);
    $this->showContent('ver', $datos);
    $this->showFooter();
  }
}
