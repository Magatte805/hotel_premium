<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:create-admin',
    description: 'Créer (ou mettre à jour) un compte administrateur en interne.',
)]
class CreateAdminCommand extends Command
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly UserPasswordHasherInterface $hasher,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('email', null, InputOption::VALUE_REQUIRED, 'Email admin')
            ->addOption('password', null, InputOption::VALUE_REQUIRED, 'Mot de passe admin')
            ->addOption('first-name', null, InputOption::VALUE_OPTIONAL, 'Prénom', 'Admin')
            ->addOption('last-name', null, InputOption::VALUE_OPTIONAL, 'Nom', 'Hotel')
            ->addOption('phone', null, InputOption::VALUE_OPTIONAL, 'Téléphone', '0000000000');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $email = (string) $input->getOption('email');
        $plainPassword = (string) $input->getOption('password');

        if ($email === '' || $plainPassword === '') {
            $io->error('Options requises: --email et --password');
            return Command::INVALID;
        }

        $firstName = (string) $input->getOption('first-name');
        $lastName = (string) $input->getOption('last-name');
        $phone = (string) $input->getOption('phone');

        $repo = $this->em->getRepository(User::class);
        $user = $repo->findOneBy(['email' => $email]);

        if (!$user) {
            $user = new User();
            $user->setEmail($email);
        }

        $user
            ->setFirstName($firstName)
            ->setLastName($lastName)
            ->setPhone($phone)
            ->setRoles(['ROLE_ADMIN']);

        $user->setPassword($this->hasher->hashPassword($user, $plainPassword));

        $this->em->persist($user);
        $this->em->flush();

        $io->success('Admin prêt: '.$email);
        return Command::SUCCESS;
    }
}




