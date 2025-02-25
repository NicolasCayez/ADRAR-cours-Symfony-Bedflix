<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

use App\Form\UserFormType;
use App\Entity\User;

use Karser\Recaptcha3Bundle\Validator\Constraints\Recaptcha3Validator;

use Doctrine\ORM\EntityManagerInterface;
use SebastianBergmann\Environment\Console;

final class UserController extends AbstractController
{
    #[Route('/user/register', name: 'register')]
    public function register(Request $request, EntityManagerInterface $entityManager, Recaptcha3Validator $recaptcha3Validator): Response
    {
        $user = new User();
        $form = $this->createForm(UserFormType::class, $user);
        $form->handleRequest($request);
        $msg = "toto";
        if ($form->isSubmitted() && $form->isValid()){
            $entityManager->persist($user);
            $entityManager->flush();
            //test l'utilisateur est un bot
            if ($recaptcha3Validator == null){
                $msg = "nul";
            }
            if ($recaptcha3Validator->getLastResponse()->getScore() < 0.5) {
                $msg = "L'utilisateur est un bot"; $notice = "danger";
            } else {
                $msg = "L'utilisateur est un humain";
            }
        }


        return $this->render('user/index.html.twig', [
            'form' => $form->createView(),
            'msg' => $msg
        ]);
    }
}
