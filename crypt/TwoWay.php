<?php
namespace um;

/**
* Class to provide 2 way encryption of data
*/
class TwoWay
{
	private $key;
	private $iv;
	protected $crypt_method = MCRYPT_RIJNDAEL_128;
	protected $crypt_mode = MCRYPT_MODE_CBC;

	public function __construct($key, $iv='')
	{
		if(!$iv)
		{
			$iv_size = mcrypt_get_iv_size($this->crypt_method, $this->crypt_mode);
			$this->iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
		}
		else
		{
			$this->iv = $iv;
		}
		$this->key = substr(md5($key), 0, mcrypt_get_key_size($this->crypt_method, $this->crypt_mode));
	}

	public function getIV()
	{
		return $this->iv;
	}

    /**
    * Encrypt a string
    *
    * @access    public
    * @param    string    $text
    * @return    string    The encrypted string
    */
    public function encrypt( $text )
    {
        $data = mcrypt_encrypt($this->crypt_method, $this->key, $text, $this->crypt_mode, $this->iv );
        return base64_encode($data);
    }

    /**
    * Decrypt a string
    *
    * @access    public
    * @param    string    $text
    * @return    string    The decrypted string
    */
    public function decrypt( $data )
    {
        $text = base64_decode($data);
        return mcrypt_decrypt($this->crypt_method, $this->key, $text, $this->crypt_mode, $this->iv );
    }
}