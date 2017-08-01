<?php

namespace TaskTrackBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class AppUpdateTokenBlacklistCommand extends ContainerAwareCommand
{
    
    protected function configure()
    {
        $this
            ->setName('app:update-token-blacklist')
            ->setDescription('...')
            ->addArgument('argument', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $base = "http://localhost:1337/tokens";
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $base);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $output = json_decode(curl_exec($ch));
        
        $now = new \DateTime;
        foreach($output as $token) {
            $createdAt = new \DateTime($token->createdAt);
            $diff = date_diff($now, $createdAt);
            dump($diff);
            if($diff->h >= 1) {
                curl_setopt($ch, CURLOPT_URL, $base);
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
                curl_setopt($ch, CURLOPT_POSTFIELDS, ["id" => $token->id]);
                curl_exec($ch);
            }
        }
        curl_close($ch);
    }

}
