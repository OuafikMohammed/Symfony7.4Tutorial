<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * UrlGenerationCommand demonstrates URL generation in Console Commands
 * 
 * Commands run outside the HTTP context (no web request), so URLs
 * need special handling. By default, they use 'localhost' as the domain.
 * You need to configure 'default_uri' in routing config to set the real domain.
 */
#[AsCommand(
    name: 'app:url-generation-demo',
    description: 'Demonstrates how to generate URLs in console commands'
)]
class UrlGenerationCommand extends Command
{
    // STEP 1: Inject the UrlGeneratorInterface service
    // Just like in services, we type-hint in the constructor
    public function __construct(
        private UrlGeneratorInterface $urlGenerator,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('URL Generation in Console Commands');

        // STEP 2: Generate a simple URL
        // In console context, this becomes: http://localhost/user/register
        // (until you configure default_uri in routing config)
        $signUpUrl = $this->urlGenerator->generate('user_register');
        $io->writeln('Sign-up URL: ' . $signUpUrl);

        // STEP 3: Generate a URL with parameters
        // Result: http://localhost/user/john-doe
        $userProfileUrl = $this->urlGenerator->generate('user_profile', [
            'username' => 'john-doe',
        ]);
        $io->writeln('User Profile URL: ' . $userProfileUrl);

        // STEP 4: Generate ABSOLUTE URL for command context
        // Important: In commands, you usually want ABSOLUTE_URL because
        // you might be sending the URL in an email or webhook
        // Without configuration, Result: http://localhost/user/register
        // With configuration: https://example.com/user/register
        $absoluteSignUpUrl = $this->urlGenerator->generate(
            'user_register',
            [],
            UrlGeneratorInterface::ABSOLUTE_URL
        );
        $io->writeln('Absolute Sign-up URL: ' . $absoluteSignUpUrl);

        // STEP 5: Generate localized URL
        // Commands can generate URLs in different languages
        $dutchUrl = $this->urlGenerator->generate('user_register', [
            '_locale' => 'nl',
        ]);
        $io->writeln('Dutch URL: ' . $dutchUrl);

        // STEP 6: Practical example - bulk email generation
        $io->section('Generating URLs for bulk email send-out');
        
        // Simulate processing multiple users
        $users = [
            ['id' => 1, 'username' => 'alice', 'email' => 'alice@example.com'],
            ['id' => 2, 'username' => 'bob', 'email' => 'bob@example.com'],
            ['id' => 3, 'username' => 'charlie', 'email' => 'charlie@example.com'],
        ];

        $emailTable = [];
        foreach ($users as $user) {
            // Generate a confirmation link for each user
            // This would be sent in their welcome email
            $confirmationLink = $this->urlGenerator->generate(
                'email_verify',
                ['userId' => $user['id']],
                UrlGeneratorInterface::ABSOLUTE_URL
            );

            $emailTable[] = [
                $user['username'],
                $user['email'],
                $confirmationLink,
            ];
        }

        // Display the generated links in a nice table format
        $io->table(
            ['Username', 'Email', 'Verification Link'],
            $emailTable
        );

        // STEP 7: Demonstrate URL types in commands
        $io->section('Different URL types');
        
        $paths = [
            'ABSOLUTE_PATH (default, not useful in commands)' => $this->urlGenerator->generate(
                'blog',
                [],
                UrlGeneratorInterface::ABSOLUTE_PATH
            ),
            'ABSOLUTE_URL (useful for emails)' => $this->urlGenerator->generate(
                'blog',
                [],
                UrlGeneratorInterface::ABSOLUTE_URL
            ),
            'NETWORK_PATH (protocol-relative)' => $this->urlGenerator->generate(
                'blog',
                [],
                UrlGeneratorInterface::NETWORK_PATH
            ),
        ];

        foreach ($paths as $type => $url) {
            $io->writeln(sprintf('<info>%s:</info> %s', $type, $url));
        }

        // STEP 8: Generate batch report links
        $io->section('Generating batch processing URLs');
        
        // Example: generate URLs for batch reports
        $batchId = 'batch_' . date('Y-m-d_H-i-s');
        $statusLink = $this->urlGenerator->generate(
            'batch_status',
            ['batchId' => $batchId],
            UrlGeneratorInterface::ABSOLUTE_URL
        );
        
        $io->success(sprintf(
            'Batch processing started. Monitor progress at: %s',
            $statusLink
        ));

        return Command::SUCCESS;
    }
}
