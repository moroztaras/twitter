<?php

namespace App\Manager;

use App\Components\Utils\TokenGenerator;
use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

/**
 * Class EmailManager.
 */
class EmailManager
{
    /**
     * EmailManager constructor.'.
     */
    public function __construct(
        private ManagerRegistry $doctrine,
        //        private \Swift_Mailer $mailer,
        //        private  \Twig_Environment $twig,
        private RouterInterface $router,
    ) {
    }

    public function sendEmailRecoverPassword(User $user): bool
    {
        $token = TokenGenerator::generateToken();

        $url = $this->router->generate('app_new_password', ['email' => $user->getEmail(), 'token' => $token], UrlGeneratorInterface::ABSOLUTE_URL);
        $fullName = $user->getFirstName().' '.$user->getLastName();

        try {
            //            $template = $this->twig->render('User/MailTemplate/recover.html.twig', [
            //                'url' => $url,
            //                'fullName' => $fullName,
            //            ]);
        } catch (\Exception $e) {
            return false;
        }

        //        $mail = new \Swift_Message();
        //        $mail->setFrom('support@socaial_network.dev');
        //        $mail->setTo($user->getEmail());
        //        $mail->setSubject('Recover password');
        //        $mail->setBody($template);

        $user->setTokenRecover($token);
        $this->doctrine->getManager()->persist($user);
        $this->doctrine->getManager()->flush();

        //        return (bool) $this->mailer->send($mail);
        return true;
    }
}
