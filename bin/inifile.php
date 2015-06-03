<?php
if (!defined("INI_FILE_PHP_CLASS"))
{
   define("INI_FILE_PHP_CLASS",true);

   //
   // inifile.php
   // -----------
   //
   // A class to manipulate/read/write configuration files
   // with the .ini format used by Windows(tm) family.
   //
   // By Hugo Gonçalves (hugo_goncalves@portugalmail.pt) - 2002/2004
   //
   // Thanks to all the PHP Classes members who send comments and bug reports and corrections.
   // From now on i will add the names of people who made great contributions to this class.
   // If I forgot anyone please mail me.
   // Thanks again!
   //
   // -------------------------------------------------------------------------------------------------------
   //
   // Contributors and testers:
   //
   // Bogdan Morar (office@brasovonline.com) - corrected some bugs about file opening, closing and reading
   //										   (prefix file functions with @ so users of phplib won't get
   //		                                   errors on HTML headers) and suggested to write a newline character (\n) 
   //		                                   between each section. Noticed that fgets should have the length 
   //										   parameter if PHP version < 4.2.0, so I added the class variable
   //										   max_len (which can be changed in the constructor) to allow bigger 
   //										   lines if required	
   //
   // Herman Kuiper (herman@ozuzo.net)		 - added the methods sectionExists, valueExists, getSections
   //                                          and reported some bugs of PHP's built-in parse_ini_file
   //                                          which lead me to make my own version
   //
   // Alessandro Rosa (zandor_zz@yahoo.it>   - suggested me to change the default line separator \n to
   //                                           \r\n, so I've added the method
   //                                           setLineSeparator which allows the user
   //                                          to change the default of the class
   //
   // -------------------------------------------------------------------------------------------------------
   //   
   class IniFile
   {
      var $filename  = "";    		// Filename to use
      var $results   = array();  	// Array that stores the resulsts
      var $loaded    = false; 		// Is file already loaded?
	  var $my_parser = true;		// Use my version of parse_ini_file or PHP's built-in function
      var $max_len;                 // The maximum length of each line of the file
      var $ls = "\n";               // The line separator when writing (by default is a \n)

      // Constructor - requires filename, optional maximum length of each line of the file
      function IniFile($filename, $max_len = 4096)
      {
	  	 $this->max_len = $max_len;
         $this->setFile($filename);
      }

      // Use this function to change the default line separator (\n) to
      // another (for example in Windows you can use \r\n and on a Mac
      // you can use \r)
      function setLineSeparator($ls)
      {
        $this->ls = $ls;
      }

	  // Set class to use my parser or PHP's built-in parse_ini_file function
	  function setNewParser($flag = true)
	  {
	  	$this->my_parser = $flag;
	  }
	  
      // Set the file to use (and clearing the buffer if desired)
      function setFile($filename, $clear = true)
      {
         $this->filename = $filename;
	 	 if($clear == true) $this->clear();
      }

      // Clear buffer
      function clear()
      {
         $this->loaded = false;
         unset($this->results);
      }

	 // My implementation of parse_ini_file
	 function parse_ini_file($filename)
	 {
		// Alocate the result array
		$res = array();
		// Does the file exists and can we read it?
		if(file_exists($filename) && is_readable($filename))
		{
			// In the beggining we are not in a section
			$section = "";
			// Open the file
			$fd = @fopen($filename,"r");
			// Read each line
			while(!feof($fd))
			{
				// Read the line and trim it
				$line = trim(@fgets($fd,$this->max_len));
				$len = strlen($line);
				// Only process non-blank lines
				if($len != 0)
				{
					// Only process non-comment lines
					if($line[0] != ';')
					{
						// Found a section?
						if(($line[0] == '[') && ($line[$len-1] == ']'))
						{
							// Get section name
							$section = substr($line,1,$len-2);
							// Check if the section is already included in result array						
							if(!isset($res[$section]))
							{
								// If not included create it
								$res[$section] = array();
							}
						}
						// Check for entries
						$pos = strpos($line,'=');
						// Found an entry
						if($pos != false)
						{
							// Get name of entry
							$name = substr($line,0,$pos);
							// Get value of entry
							$value = substr($line,$pos+1,$len-$pos-1);
							// Store entry
							// If we are inside a section
							if($section != "")
							{
								$res[$section][$name] = $value;
							}
							else						
							{
								$res[$name] = $value;
							}
						}
					}
				}				
			}
			// Close the file
			@fclose($fd);
		}
		return $res;
	}
	  
      // Loads a file
      function loadFile()
      {
         // Check if file exists and if its readable
         if(file_exists($this->filename) && is_file($this->filename) &&
            is_readable($this->filename))
         {
            // Clears results
            $this->clear();
            // Parse the ini file
			if($this->my_parser == true)
			{
            	$this->results = $this->parse_ini_file($this->filename);
			}
			else
			{
            	$this->results = parse_ini_file($this->filename,true);
			}
            // Mark loaded flag
            $this->loaded = true;
         }
      }

      // Returns the value of an option ($option) in a given section ($section)
      // or an empty string if it was not found
      function getValue($section, $option)
      {
         // If the file is not loaded yet...
         if(!($this->loaded))
         {
            // ...load it to memory
            $this->loadFile();
         }
         // Does section exists in results array?
         if(isset($this->results[$section]))
         {
            // Get the values of that section from results array
            $sectionValues = $this->results[$section];
            // Test if the result for that section is an array
            if(is_array($sectionValues))
            {
               // It's an array
               // Now check if the index $option is defined
               if(isset($sectionValues[$option]))
               {
                  // Done. Return the value in that position
                  return $sectionValues[$option];
               }
               else
               {
                  // No, the option in that section does not exist.
                  // Return an empty string
                  return "";
               }
            }
            // Is not an array, so section isn't really a section
            // but an option not belonging to any section
            else
            {
               // Return the value
               return $sectionValues;
            }
         }
         // Did not find any section or option...
         else
         {
            // Return an empty section
            return "";
         }
      }

      // Check if section or value exists
      function sectionExists($section)
      {
         // If the file is not loaded yet...
         if(!($this->loaded))
         {
            // ...load it to memory
            $this->loadFile();
         }

         return isset($this->results[$section]);
      }

      // Check if section or value exists
      function valueExists($section, $option)
      {
         if($this->sectionExists($section))
            return isset($this->results[$section][$option]);
         else
            return false;
      }

      // Returns an array with all the pairs option => value of a section
      function getSection($section)
      {
         // If the file is not loaded yet...
         if(!($this->loaded))
         {
            // ...load it to memory
            $this->loadFile();
         }
         // Does section exists in results array?
         if(isset($this->results[$section]))
         {
            // Get the values of that section from results array
            $sectionValues = $this->results[$section];
            // Test if the result for that section is an array
            if(is_array($sectionValues))
            {
               // Return the values of that section
               return $sectionValues;
            }
            // Is not an array, so section isn't really a section
            // but an option not belonging to any section
            else
            {
               $tmp = array();
               $tmp[$section] = $sectionValues;
               return $tmp;
            }
         }
         // Did not find any section or option...
         // ...return an empty array
         return array();
      }

      // Retrieve an array of section identifiers
      function getSections()
      {
         // If the file is not loaded yet...
         if(!($this->loaded))
         {
            // ...load it to memory
            $this->loadFile();
         }

         return isset($this->results) ? array_keys($this->results) : array();
      }

      // Sets or changes the value of an option ($option) in a given section ($section).
      // If $section is an empty string it sets/changes an option not belonging to
      // any section.
      // If $write is true then it updates the file in disk, else only in memory
      // (this is the default behaviour)
      function setValue($section, $option, $value, $write = false)
      {
         // If section is not null then store value in the section
         if($section != "")
         {
            $this->results[$section][$option] = $value;
         }
         // Else, store value outside any section
         else
         {
            $this->results[$option] = $value;
         }
         if($write == true)
         {
            return $this->writeFile();
         }
         return true;
      }

      // Writes the buffer to the file.
      // Note, it recreates the file, so any comments and empty lines are lost
      function writeFile()
      {
         // Write success flag
         $ok = true;
         // Open file for writing
         $fp = @fopen($this->filename,"wb");
         // If failed return false
         if(!$fp)
         {
            $ok = false;
         }
         else
         {
            // Do we have anything to write?
            if(isset($this->results))
            {
               // Iterate through the results
               // Get each section and its values
               while(list($section, $values) = each($this->results))
               {
                  // Is the contents of this section an array
                  if(is_array($values))
                  {
                     // Write section name
                     $res = @fwrite($fp,"[$section]".$this->ls);
                     // File error trapping
                     if($res == -1)
                     {
                        $ok = false;
                        break;
                     }
                     // Iterate through section
                     // Get each option and value
                     while(list($option, $value) = each($values))
                     {
                        // Write option
                        $res = @fwrite($fp,"$option=$value".$this->ls);
                        // File error trapping
                        if($res == -1)
                        {
                           $ok = false;
                           break;
                        }
                     }
                     // Write a blank line between sections
                     $res = @fwrite($fp,$this->ls);
                     // File error trapping
                     if($res == -1)
                     {
                        $ok = false;
                        break;
                     }
                     if($ok == false) break;
                  }
                  // If contents is not an array
                  else
                  {
                     // Write option not belonging to any section
                     $res = @fwrite($fp,"$section=$values".$this->ls);
                     // File error trapping
                     if($res == -1)
                     {
                        $ok = false;
                        break;
                     }
                  }
               }
            }
            // Flush file
            @fflush($fp);
            // Close file
            if(@fclose($fp) == false)
            {
               $ok = false;
            }
         }
         // Return status
         return $ok;
      }
   }

   // Auxiliar function to get a value from an .ini file
   // without creating instances of the class IniFile
   function getValueFromINI($filename, $section, $option)
   {
      // Create a new instance
      $inifile = new IniFile($filename);
      // Call method to retrieve value
      return $inifile -> getValue($section,$option);
   }
}
?>


