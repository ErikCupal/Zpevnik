<?php
    require_once 'makeTextNiceAgain.php';

    class Song
    {
        private $base_filename;
        private $title;
        private $artist;
        private $has_pdf_gen;
        private $date_added;
        private $language;
        private $id;

        function __construct($id, $title, $artist, $has_pdf_gen, $date_added, $language)
        {
            $this->id=$id;
            $this->title = $title;
            $this->artist = $artist;
            $this->has_pdf_gen = $has_pdf_gen;
            $this->date_added = $date_added;
            $this->language = $language;
            $this->base_filename = "PDFs/" . makeTextNiceAgain($this->artist . "_" . $this->title);
        }

        /**
         * @return mixed
         */
        public function getId()
        {
            return $this->id;
        }

        /**
         * @return String
         */
        public function getTitle()
        {
            return $this->title;
        }

        /**
         * @return String
         */
        public function getArtist()
        {
            return $this->artist;
        }

        /**
         * @return int
         */
        public function getDateAdded()
        {
            return $this->date_added;
        }

        /**
         * @return String
         */
        public function getLanguage()
        {
            return $this->language;
        }

        private function getSkenURL()
        {
            return $this->base_filename . "-sken.pdf";
        }

        private function getCompURL()
        {
            return $this->base_filename . "-comp.pdf";
        }

        private function getGenURL()
        {
            return $this->base_filename . "-gen.pdf";
        }

        public function getGenLink()
        {
            if ($this->has_pdf_gen == 0) {
                return "---";
            } else {
                return "<a href=\"" . $this->getGenURL() . "\">GEN</a>";
            }
        }

        public function getSkenLink(){
            return "<a href=\"" . $this->getSkenURL() . "\">ORIGINAL</a>";
        }

        public function getCompLink(){
            return "<a href=\"" . $this->getCompURL() . "\">COMP</a>";
        }

    }