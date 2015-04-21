<?php
return [

//------------------------//
// SYSTEM SETTINGS
//------------------------//
	'API_KEY'=>'REDACTED',
	'tiers'=>['UNRANKED'=>0, 'BRONZE'=>1, 'SILVER'=>2, 'GOLD'=>3, 'PLATINUM'=>4, 'DIAMOND'=>5, 'MASTER'=>6, 'CHALLENGER'=>7],
	'tiers_rev'=>[0=>'Unranked', 1=>'Bronze', 2=>'Silver', 3=>'Gold', 4=>'Platinum', 5=>'Diamond', 6=>'Master', 7=>'Challenger'],
	'divisions'=>['V'=>5, 'IV'=>4, 'III'=>3, 'II'=>2, 'I'=>1],
    /**
     * Registration Needs Activation.
     *
     * If set to true users will have to activate their accounts using email account activation.
     */
    'rna' => false,

    /**
     * Login With Email.
     *
     * If set to true users will have to login using email/password combo.
     */
    'lwe' => false, 

    /**
     * Force Strong Password.
     *
     * If set to true users will have to use passwords with strength determined by StrengthValidator.
     */
    'fsp' => false,

    /**
     * Set the password reset token expiration time.
     */
    'user.passwordResetTokenExpire' => 3600,

//------------------------//
// EMAILS
//------------------------//

    /**
     * Email used in contact form.
     * Users will send you emails to this address.
     */
    'adminEmail' => 'REDACTED', 

    /**
     * Not used in template.
     * You can set support email here.
     */
    'supportEmail' => 'REDACTED',
];
