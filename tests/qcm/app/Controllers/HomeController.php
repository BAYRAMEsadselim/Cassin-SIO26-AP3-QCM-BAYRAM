<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Security\Auth;
use App\Core\Controller;

final class HomeController extends Controller
{
    public function index(): void
    {
        // Page d'accueil par rôle. Les routes formateur/étudiant restent à créer.
        $accueil = match (Auth::user()['role']) {
            'admin'     => '/admin/matieres',
            'formateur' => '/formateur/qcm',
            'etudiant'  => '/etudiant/qcm',
        };
        $this->redirect($accueil);
    }
}
