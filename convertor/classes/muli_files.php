<?php

class muli_files extends file_upload {
	
	var $number_of_files = 0;
	var $names_array;
	var $tmp_names_array;
	var $error_array;
	var $wrong_extensions = 0;
	var $bad_filenames = 0;
	
	function extra_text($msg_num) {
		switch ($this->language) {
			case "de":
			// add you translations here
			break;
			default:
			$extra_msg[1] = "Error for: <b>".$this->the_file."</b>";
			$extra_msg[2] = "You have tried to upload ".$this->wrong_extensions." files with a bad extension, the following extensions are allowed: <b>".$this->ext_string."</b>";
			$extra_msg[3] = "Select at least on file.";
			$extra_msg[4] = "Select the file(s) for upload.";
			$extra_msg[5] = "You have tried to upload <b>".$this->bad_filenames." files</b> with invalid characters inside the filename.";
		}
		//if(isset($extra_msg[$msg_num]))$this->is_error = true;
		return $extra_msg[$msg_num];
	}
	// this method checkes the number of files for upload
	// this example works with one or more files
	function count_files() {
		foreach ($this->names_array as $test) {
			if ($test != "") {
				$this->number_of_files++;
			}
		}
		if ($this->number_of_files > 0) {
			return true;
		} else {
			return false;
		} 
	}
	function upload_multi_files () {
		$this->message = "";
		if ($this->count_files()) {
			foreach ($this->names_array as $key => $value) { 
				if ($value != "") {
					$this->the_file = $value;
					$new_name = $this->set_file_name();
					if ($this->check_file_name($new_name)) {
						if ($this->validateExtension()) {
							$this->file_copy = $new_name;
							$this->the_temp_file = $this->tmp_names_array[$key];
							if (is_uploaded_file($this->the_temp_file)) {
								if ($this->move_upload($this->the_temp_file, $this->file_copy)) {
									$this->message[] = $this->error_text($this->error_array[$key]);
									if ($this->rename_file) $this->message[] = $this->error_text(16);
									//sleep(1); // wait a seconds to get an new timestamp (if rename is set)
								}
							} else {
								$this->message[] = $this->extra_text(1);
								$this->message[] = $this->error_text($this->error_array[$key]);
							}
						} else {
							$this->wrong_extensions++;
						}
					} else {
						$this->bad_filenames++;
					}
				} 
			}
			if ($this->bad_filenames > 0) $this->message[] = $this->extra_text(5);
			if ($this->wrong_extensions > 0) {
				$this->show_extensions();
				$this->message[] = $this->extra_text(2);
			}
		} else {
			$this->message[] = $this->extra_text(3);
		}
	}
}

