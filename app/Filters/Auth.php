<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class Auth implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $uri      = service('uri');
        $uri      = $uri->getSegment(1);
        $this->db = db_connect();
        $session  = session();
        $id_user  = $session->get('id');
        $akses    = $this->db->query("SELECT * FROM akses_menu WHERE id_user = '$id_user'")->getRow();

        if (!$session->get('@#)login(#@')) {
            return redirect()->to(base_url());
        } else if ($uri == 'dashboard' || $uri == 'kasir') {
            $akses = explode(',', $akses->menu);
            $app_menu = [];
            $menu = $this->db->table("app_menu")->select("link")->whereIn("id", $akses)->get()->getResult();
            foreach ($menu as $key) {
                array_push($app_menu, $key->link);
            }
            if (!in_array($uri, $app_menu)) {
                return redirect()->back();
            }
        } else {
            $akses = explode(',', $akses->child);
            $app_child = [];
            $child = $this->db->table("app_child_menu")->select("link")->whereIn("id", $akses)->get()->getResult();
            foreach ($child as $key) {
                array_push($app_child, $key->link);
            }
            if (!in_array($uri, $app_child)) {
                return redirect()->back();
            }
        }
    }
    //--------------------------------------------------------------------

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do something here
    }
}
