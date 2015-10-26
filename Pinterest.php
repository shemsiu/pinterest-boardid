<?php

class Pinterest
{
    protected $url = "";
    protected $filename = "";

    public $id = 0;
    protected $error = false;
    protected $error_message = "";

    public function __construct($url)
    {
        $this->url = $url;
        $this->filename = md5(uniqid("filename")) . '.txt';

        # Save the whole page into a txt file
        $this->savePinterestToTxt();

        # Make some regular expression in the txt file to find the board id
        # If the url is correct and the board exists, then return it - otherwise return error message
        $this->findBoardIdInTxt();

        # Remove the temporary file
        unlink($this->filename);
    }

    private function savePinterestToTxt()
    {
        $ch = curl_init($this->url);
        $fp = fopen($this->filename, "w");

        curl_setopt($ch, CURLOPT_FILE, $fp);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        curl_exec($ch);
        curl_close($ch);
        fclose($fp);
    }

    private function findBoardIdInTxt()
    {
        if (file_exists($this->filename)) {
            $search = '"board", "id: "';
            $contents = file_get_contents($this->filename);
            $pattern = "/^.*$pattern.*\$/m";

            if (preg_match_all($pattern, $contents, $matches)) {

                #We only need to read a few lines from the txt file
                $text = substr(implode("\n", $matches[0]), 1500, 1000);

                # Run our new regular expression with the new pattern and search after  "board", "id": "get-the-id-here",
                if (preg_match("/\"board\", \"id\": \"\s*(((?!board\", \"id\": \"|\", \"name\":).)+)\s*\", \"name\":/m", $text, $out)) {
                    $this->id = $out[1];
                } else {
                    $this->error_message = "The Pinterest Board is not valid.";
                    $this->error = true;
                }
            } else {
                $this->error_message = "No matches found";
                $this->error = true;
            }
        }
    }

    public function __toString()
    {
        if (!$this->error) {
            return $this->id;
        } else {
            return $this->error_message;
        }
    }
}
