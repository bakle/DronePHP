<?php
/**
 * DronePHP (http://www.dronephp.com)
 *
 * @link      http://github.com/fermius/DronePHP
 * @copyright Copyright (c) 2016 DronePHP. (http://www.dronephp.com)
 * @license   http://www.dronephp.com/license
 */

class Drone_FileSystem_Shell implements Drone_FileSystem_ShellInterface
{
	/**
	 * Home directory (~)
	 *
	 * @var string
	 */
	private $home;

	/**
	 * Result of last command (optional)
	 *
	 * @var string
	 */
	private $buffer = null;

	/**
	 * Returns the home attribute
	 *
	 * @return string
	 */
	public function getHome()
	{
		return $this->home;
	}

	/**
	 * Returns the buffer attribute
	 *
	 * @return mixed
	 */
	public function getBuffer()
	{
		return $this->buffer;
	}

    /**
     * Constructor
     *
     * @param string $home
     */
	public function __construct($home = null)
	{
		$this->home = (is_null($home) || empty($home)) ? $this->pwd() : $home;
	}

	/**
	 * Returns the curent directory
	 *
	 * @return string|boolean
	 */
	public function pwd()
	{
		if (getcwd())
			$this->buffer = getcwd();
		else
			return false;

		return $this->buffer;
	}

	/**
	 * Returns a list with directory contents
	 *
	 * @param string|null $path
	 * @param boolean     $recursive
	 *
	 * @return array
	 */
	public function ls($path, $recursive = false)
	{
		$filesToReturn = array();

		$path = (is_null($path) || empty($path)) ? '.' : $path;

		if (is_file($path))
			$filesToReturn = array($path);
		elseif (is_dir($path))
		{
			$pathIns = dir($path);

			if ($recursive)
			{
                if ($handle = opendir($path)) {

                    while (false !== ($item = readdir($handle))) {

                        if (strstr($item,'~') === false && $item != '.' && $item != '..')
                        {
                            if (is_dir($path . "/" . $item))
                                $filesToReturn = array_merge($filesToReturn, $this->ls($path . "/" . $item, true));

                            $filesToReturn[] = $path . "/" . $item;
                        }
                    }

                    closedir($handle);
                }
   			}
			else {
				while (false !== ($item = $pathIns->read())) {
					$filesToReturn[] = $item;
				}
				$pathIns->close();
			}
		}
		else {

			$pathIns = dir('.');
			$contents = $this->ls('.');

			foreach ($contents as $item)
			{
				if (!empty($path))
					if (!strlen(stristr($item, $path)) > 0)
						continue;
				if (strstr($item,'~') === false && $item != '.' && $item != '..')
					$filesToReturn[] = $item;
			}
		}

		return $filesToReturn;
	}

	/**
	 * Changes the current directory
	 *
	 * @param boolean|null $path
	 *
	 * @return boolean
	 */
	public function cd($path)
	{
		$moveTo = (is_null($path) || empty($path)) ? $this->home : $path;

		if (is_dir($path))
		{
			if (!chdir($moveTo))
				return false;
		}

		return false;
	}

	/**
	 * Creates a file
	 *
	 * @param string
	 *
	 * @return boolean
	 */
	public function touch($file)
	{
		if (file_exists($file))
		{
			if (!$openFile = fopen($file, 'w+'))
				return false;

			if (fwrite($openFile, ""))
				return true;

			fclose($openFile);
		}

		return false;
	}

	/**
	 * Deletes one or more files
	 *
	 * @param string       $file
	 * @param boolean|null $recursive
	 *
	 * @return boolean
	 */
	public function rm($file, $recursive = null)
	{
		$recursive = is_null($recursive) ? false : $recursive;

		if (is_null($file))
			throw new Exception("Missing parameter for rm!");

		if (file_exists($file) && !$recursive)
			unlink($file);
		elseif (is_dir($file) && $recursive)
		{
            if ($handle = opendir($file)) {

                while (false !== ($item = readdir($handle))) {

                    if (strstr($item,'~') === false && $item != '.' && $item != '..')
                    {
                    	if (is_dir($file ."/". $item))
                    		$this->rm($file ."/". $item, true);
                    	elseif (is_file($file ."/". $item))
                    		$this->rm($file ."/". $item);

                    	if (is_dir($file ."/". $item))
                    		rmdir($file ."/". $item);
                    }
                }

                rmdir($file);

                closedir($handle);
            }
		}

		return true;
	}

	/**
	 * Copies one or more files
	 *
	 * @param string       $file
	 * @param string       $dest
	 * @param boolean|null $recursive
	 *
	 * @return boolean
	 */
	public function cp($file, $dest, $recursive = null)
	{
		$recursive = (is_null($recursive)) ? false : $recursive;

		if (empty($file) || empty($dest))
			throw new Exception("Missing parameters!");

		if (is_dir($file))
		{
			if ( (!file_exists($dest) || (file_exists($dest) && is_file($dest)) ) && $recursive)
				mkdir($dest, 0777, true);

            if ($handle = opendir($file)) {

                while (false !== ($item = readdir($handle))) {

                    if (strstr($item,'~') === false && $item != '.' && $item != '..')
                    {
                    	if (is_dir($file ."/". $item) && $recursive)
                    	{
                    		mkdir($dest ."/". $item, 0777, true);
                    		$this->cp($file ."/". $item, $dest ."/". $item, true);
                    	}
                    	elseif (is_file($file ."/". $item))
                    		$this->cp($file ."/". $item, $dest ."/". $item);
                    }
                }

                closedir($handle);
            }
		}
		else
		{
			if (file_exists($dest) && is_dir($dest))
				copy($file, $dest.'/'. basename($file));
			else
				copy($file, $dest);
		}

		return true;
	}

	/**
	 * Moves or renames files
	 *
	 * @param string $oldfile
	 * @param string $newfile
	 *
	 * @return boolean
	 */
	public function mv($oldfile, $newfile)
	{
		if (empty($oldfile))
			throw new Exception("Missing parameter for mv!");

		if (is_dir($newfile))
				$newfile .= '/'.basename($oldfile);

		if ($oldfile == $newfile)
			return $this;

		if(!rename($oldfile, $newfile))
			return false;

		return true;
	}

	/**
	 * Creates a directory
	 *
	 * @param string       $dir
	 * @param string|null  $dest
	 * @param booelan|null $recursive
	 *
	 * @return boolean
	 */
	public function mkdir($dir, $dest, $recursive = null)
	{
		if (empty($dir))
			throw new Exception("Missing parameter for mkdir!");

		if (empty($dest))
			$dest = '.';

		$recursive = (is_null($recursive)) ? false : $recursive;

		if ($recursive)
			mkdir("$dest/$dir", 0777, true);
		else {
			if (!is_dir($dir))
			{
				if(!mkdir("$dir", 0777))
					return false;
			}
		}

		return true;
	}

	/**
	 * Deletes a directory
	 *
	 * @param string $dir
	 *
	 * @return boolean
	 */
	public function rmdir($dir)
	{
		if (is_null($dir) || empty($dir))
			throw new Exception("Missing parameter for rmdir!");

		if (rmdir($dir))
			return true;
		else
			return false;
	}
}