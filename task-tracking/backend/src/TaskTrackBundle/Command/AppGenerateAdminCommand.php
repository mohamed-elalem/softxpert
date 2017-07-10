<?php

namespace TaskTrackBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use TaskTrackBundle\Constants\Role;
use TaskTrackBundle\Entity\User;
use TaskTrackBundle\Repository\UserRepository;

class AppGenerateAdminCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('app:generate-admin')
            ->setDescription('Initiating database with an admin user')
            ->addArgument('argument', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option', null, InputOption::VALUE_NONE, 'Option description')
            ->setHelp("This command generates an admin with a given credintials username: admin, password: 123456\nYou may change this configuration in src/TaskTrackBundle/Command/AppGenerateAdminCommand.php")
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
//        $argument = $input->getArgument('argument');
//
//        if ($input->getOption('option')) {
//            // ...
//        }
//
//        $output->writeln('Command result.');
        
        $em = $this->getContainer()->get("doctrine")->getManager();
        $encoder = $this->getContainer()->get("security.password_encoder");
        $text = null;
        $style = null;
        
        $userRepository = $this->getContainer()->get('doctrine')->getEntityManager()->getRepository('TaskTrackBundle:User');
        
        $user = $userRepository->findOneBy(["username" => "admin"]);
        
        if(! $user) {
            
            $style = new OutputFormatterStyle("white", "cyan", ["blink", "bold"]);
            
            $user = new User();
            $user->setEmail("admin@webmaster.com")
                    ->setName("Administrator")
                    ->setRole(Role::ADMIN)
                    ->setUsername("admin")
                    ->setPassword($encoder->encodePassword($user, "123456"));
            $em->persist($user);
            $em->flush();
            
            $text = "<fg=white;bg=cyan;options=blink>Admin user is successfully created</>";
        }
        else {
            $text = "<fg=white;bg=red;options=blink>Error\nAdmin already exists with id " . $user->getId() . "</>";
        }
//        $output->getFormatter()->setStyle("any", $style);
        $output->writeln($text);
    }

}
