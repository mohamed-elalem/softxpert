<?php

namespace TaskTrackBundle\Entity;

/**
 * AboutPage
 */
class AboutPage
{
    /**
     * @var int
     */
    private $id;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }
    /**
     * @var string
     */
    private $title;

    /**
     * @var string
     */
    private $content;

    /**
     * @var int
     */
    private $section;

    /**
     * @var int
     */
    private $font_size;

    /**
     * @var string
     */
    private $font_color;

    /**
     * @var string
     */
    private $font_family;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $children;

    /**
     * @var \TaskTrackBundle\Entity\AboutPage
     */
    private $parent;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->children = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set title
     *
     * @param string $title
     *
     * @return AboutPage
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set content
     *
     * @param string $content
     *
     * @return AboutPage
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set section
     *
     * @param \integer $section
     *
     * @return AboutPage
     */
    public function setSection($section)
    {
        $this->section = $section;

        return $this;
    }

    /**
     * Get section
     *
     * @return \integer
     */
    public function getSection()
    {
        return $this->section;
    }

    /**
     * Set fontSize
     *
     * @param \integer $fontSize
     *
     * @return AboutPage
     */
    public function setFontSize($fontSize)
    {
        $this->font_size = $fontSize;

        return $this;
    }

    /**
     * Get fontSize
     *
     * @return \int
     */
    public function getFontSize()
    {
        return $this->font_size;
    }

    /**
     * Set fontColor
     *
     * @param string $fontColor
     *
     * @return AboutPage
     */
    public function setFontColor($fontColor)
    {
        $this->font_color = $fontColor;

        return $this;
    }

    /**
     * Get fontColor
     *
     * @return string
     */
    public function getFontColor()
    {
        return $this->font_color;
    }

    /**
     * Set fontFamily
     *
     * @param string $fontFamily
     *
     * @return AboutPage
     */
    public function setFontFamily($fontFamily)
    {
        $this->font_family = $fontFamily;

        return $this;
    }

    /**
     * Get fontFamily
     *
     * @return string
     */
    public function getFontFamily()
    {
        return $this->font_family;
    }

    /**
     * Add child
     *
     * @param \TaskTrackBundle\Entity\AboutPage $child
     *
     * @return AboutPage
     */
    public function addChild(\TaskTrackBundle\Entity\AboutPage $child)
    {
        $this->children[] = $child;

        return $this;
    }

    /**
     * Remove child
     *
     * @param \TaskTrackBundle\Entity\AboutPage $child
     */
    public function removeChild(\TaskTrackBundle\Entity\AboutPage $child)
    {
        $this->children->removeElement($child);
    }

    /**
     * Get children
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * Set parent
     *
     * @param \TaskTrackBundle\Entity\AboutPage $parent
     *
     * @return AboutPage
     */
    public function setParent(\TaskTrackBundle\Entity\AboutPage $parent = null)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Get parent
     *
     * @return \TaskTrackBundle\Entity\AboutPage
     */
    public function getParent()
    {
        return $this->parent;
    }
}
