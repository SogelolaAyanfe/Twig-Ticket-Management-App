<?php
// src/modules/bcrypt.php

class Bcrypt {
    
    const SALT_VALUE = 10;
    
    /**
     * Hash a password
     * @param string $value - The password to hash
     * @return string - The hashed password
     */
    public static function hash($value) {
        return password_hash($value, PASSWORD_BCRYPT, ['cost' => self::SALT_VALUE]);
    }
    
    /**
     * Compare a password with a hash
     * @param string $value - The plain password
     * @param string $hash - The hashed password
     * @return bool - True if they match, false otherwise
     */
    public static function compare($value, $hash) {
        return password_verify($value, $hash);
    }
    
    /**
     * Encrypt/encode data to store in session
     * Converts data to JSON string then to base64
     * @param mixed $val - The value to encrypt
     * @return string - The encrypted value
     */
    public static function encrypt($val) {
        $jsonString = json_encode($val);
        return base64_encode($jsonString);
    }
    
    /**
     * Decrypt/decode data from session
     * Converts base64 back to original data
     * @param string $encodedVal - The encoded value
     * @return mixed - The decrypted value
     */
    public static function decrypt($encodedVal) {
        $jsonString = base64_decode($encodedVal);
        return json_decode($jsonString, true);
    }
}
?>