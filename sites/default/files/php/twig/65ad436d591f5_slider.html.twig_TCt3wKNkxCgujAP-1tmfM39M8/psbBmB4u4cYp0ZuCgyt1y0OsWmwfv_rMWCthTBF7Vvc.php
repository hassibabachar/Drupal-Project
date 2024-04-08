<?php

use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Extension\SandboxExtension;
use Twig\Markup;
use Twig\Sandbox\SecurityError;
use Twig\Sandbox\SecurityNotAllowedTagError;
use Twig\Sandbox\SecurityNotAllowedFilterError;
use Twig\Sandbox\SecurityNotAllowedFunctionError;
use Twig\Source;
use Twig\Template;

/* @vani/template-parts/slider.html.twig */
class __TwigTemplate_f77ce876afad0818a7a2248f96aee5cf extends Template
{
    private $source;
    private $macros = [];

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->parent = false;

        $this->blocks = [
        ];
        $this->sandbox = $this->env->getExtension('\Twig\Extension\SandboxExtension');
        $this->checkSecurity();
    }

    protected function doDisplay(array $context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 1
        echo "<section class=\"slider\">
  <div class=\"container slider-container\">
    <div class=\"slider-text\">
      <ul class=\"js-rotating\">
        ";
        // line 5
        if ((($context["slider_code"] ?? null) != "")) {
            // line 6
            echo "          ";
            echo $this->extensions['Drupal\Core\Template\TwigExtension']->renderVar(twig_striptags($this->sandbox->ensureToStringAllowed(($context["slider_code"] ?? null), 6, $this->source), "<ol>,<ul>,<li>,<p>,<a>,<img>,<video>,<h1>,<h2>,<h3>,<h4>,<h5>,<h6>,<em>,<strong>,<br>,<i>,<button>,<mark>,<hr>"));
            echo "
        ";
        } else {
            // line 8
            echo "        <li>
  \t\t\t\t<h1>Vani is <em>Multipurpose</em> Drupal theme</h1>
  \t\t\t\t<p>Vani Theme is packed full of all the amazing features and options for you to create</p>
  \t\t\t\t<a class=\"button-link\" href=\"#\">Get Started</a>
  \t\t\t</li>
  \t\t\t<li>
  \t\t\t\t<h1>Welcome To <em>Drupar</em> Design Studio</h1>
  \t\t\t\t<p>We present you material design. We put our hearts and soul into making every project.</p>
  \t\t\t\t<a class=\"button-link\" href=\"#\">Get Started</a>
  \t\t\t</li>
  \t\t\t<li>
  \t\t\t\t<h1>We Create <em>Awesome</em> Drupal Themes!</h1>
  \t\t\t\t<p>Our themes are of high quality, flexible and beautifully crafted that stand out of crowd.</p>
  \t\t\t\t<a class=\"button-link\" href=\"#\">Get Started</a>
  \t\t\t</li>
        ";
        }
        // line 24
        echo "      </ul> <!--/.home-slider -->
    </div><!-- /slider-text -->
    <div class=\"slider-image\">
      ";
        // line 27
        if ((($context["slider_image_path"] ?? null) != "")) {
            // line 28
            echo "        <img src=\"";
            echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(($context["slider_image_path"] ?? null), 28, $this->source), "html", null, true);
            echo "\" alt=\"slider image\" />
      ";
        } else {
            // line 30
            echo "        <img src=\"";
            echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(($context["directory"] ?? null), 30, $this->source), "html", null, true);
            echo "/images/slider.svg\" alt=\"slider image\" />
      ";
        }
        // line 32
        echo "    </div><!-- /slider-image -->
  </div> <!--/.container -->
</section>
";
    }

    public function getTemplateName()
    {
        return "@vani/template-parts/slider.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  90 => 32,  84 => 30,  78 => 28,  76 => 27,  71 => 24,  53 => 8,  47 => 6,  45 => 5,  39 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "@vani/template-parts/slider.html.twig", "C:\\xampp\\htdocs\\drupal\\drupal-10.1.7\\themes\\contrib\\vani\\templates\\template-parts\\slider.html.twig");
    }
    
    public function checkSecurity()
    {
        static $tags = array("if" => 5);
        static $filters = array("raw" => 6, "striptags" => 6, "escape" => 28);
        static $functions = array();

        try {
            $this->sandbox->checkSecurity(
                ['if'],
                ['raw', 'striptags', 'escape'],
                []
            );
        } catch (SecurityError $e) {
            $e->setSourceContext($this->source);

            if ($e instanceof SecurityNotAllowedTagError && isset($tags[$e->getTagName()])) {
                $e->setTemplateLine($tags[$e->getTagName()]);
            } elseif ($e instanceof SecurityNotAllowedFilterError && isset($filters[$e->getFilterName()])) {
                $e->setTemplateLine($filters[$e->getFilterName()]);
            } elseif ($e instanceof SecurityNotAllowedFunctionError && isset($functions[$e->getFunctionName()])) {
                $e->setTemplateLine($functions[$e->getFunctionName()]);
            }

            throw $e;
        }

    }
}
