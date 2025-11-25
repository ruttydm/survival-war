<?php
require_once 'includes/bootstrap.php';

use Nette\Forms\Form;

$form = new Form;

// Add form fields
$form->addText('player', 'Type Username Here:')
    ->setRequired('Please enter a username.')
    ->addRule($form::MIN_LENGTH, 'Username must be at least %d characters', 5)
    ->addRule($form::MAX_LENGTH, 'Username must be at most %d characters', 21)
    ->setHtmlAttribute('class', 'inline_text_inp')
    ->setHtmlAttribute('style', 'width:150px');

$form->addPassword('password', 'Type Password Here:')
    ->setRequired('Please enter a password.')
    ->setHtmlAttribute('class', 'inline_text_inp')
    ->setHtmlAttribute('style', 'width:150px');

$form->addPassword('pass2', 'Retype password:')
    ->setRequired('Please retype your password.')
    ->addRule($form::EQUAL, 'Passwords do not match', $form['password'])
    ->setHtmlAttribute('class', 'inline_text_inp')
    ->setHtmlAttribute('style', 'width:150px');

$form->addEmail('email', 'Type E-mail address:')
    ->setRequired('Please enter your email.')
    ->setHtmlAttribute('class', 'inline_text_inp')
    ->setHtmlAttribute('style', 'width:150px');

$form->addSubmit('send', 'submit')
    ->setHtmlAttribute('class', 'RedButton')
    ->setHtmlAttribute('style', 'width:80px');

// Handle submission
$form->onSuccess[] = function (Form $form, \stdClass $data) {
    $db = Database::getInstance()->getExplorer();
    
    try {
        // Check if player already exists
        if ($db->table('km_users')->where('playername', $data->player)->fetch()) {
            $form->addError('There is already a player with that name.');
            return;
        }
        
        // Check if email already exists
        if ($db->table('km_users')->where('email', $data->email)->fetch()) {
            $form->addError('There is already a player with that e-mail address.');
            return;
        }

        // Generate keys and hash
        $thekey = bin2hex(random_bytes(32));
        $hashedPassword = password_hash($data->password, PASSWORD_ARGON2ID);
        $ip = $_SERVER['REMOTE_ADDR'] ?? '';

        // Insert new user
        $db->table('km_users')->insert([
            'playername' => $data->player,
            'password' => $hashedPassword,
            'email' => $data->email,
            'validated' => '0',
            'validkey' => $thekey,
            'numturns' => '30',
            'ip' => $ip,
        ]);

        // Send activation email
        $path = defined('SITE_URL') ? SITE_URL : "http://rutgerx99.ninetynine.axc.nl";
        $activationUrl = "$path/activate.php?player=" . urlencode($data->player) . "&keynode=$thekey";
        $emailSubject = "Your Survival War Activation Key";
        $emailBody = "Welcome to Survival War!\n\nClick the link below to activate your account:\n$activationUrl\n\nIf you did not create this account, please ignore this email.";
        
        mail($data->email, $emailSubject, $emailBody, "From: " . (defined('MAIL_FROM') ? MAIL_FROM : 'noreply@survivalwar.com'));

        // Redirect to login with success message
        // We can use a session flash message or just a query param
        header('Location: login.php?registered=1');
        exit;

    } catch (\Exception $e) {
        $form->addError('An error occurred during registration. Please try again.');
        // Log the error
        Tracy\Debugger::log($e);
    }
};

// Render template
$template = TemplateEngine::getInstance();
$template->display('pages/register.latte', ['form' => $form]);