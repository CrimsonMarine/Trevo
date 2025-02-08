<?php
namespace app\controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class ContactController {
    public function contact(Request $request, Response $response) {
        view('contact', ['contact' => 'email', 'title' => 'Contact']);
        return $response;
    }

}