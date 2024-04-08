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

/* themes/contrib/vani/templates/layout/page--front.html.twig */
class __TwigTemplate_cf69ba377407c4cefe01a622732485cc extends Template
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
        // line 11
        $this->loadTemplate("@vani/template-parts/header.html.twig", "themes/contrib/vani/templates/layout/page--front.html.twig", 11)->display($context);
        // line 12
        echo "<div id=\"main-wrapper\" class=\"main-wrapper\">
  ";
        // line 13
        $this->loadTemplate("@vani/template-parts/content_home.html.twig", "themes/contrib/vani/templates/layout/page--front.html.twig", 13)->display($context);
        // line 14
        echo "  <div class=\"container\">
    ";
        // line 15
        if (($context["front_sidebar"] ?? null)) {
            // line 16
            echo "      <div class=\"main-container\">
    ";
        }
        // line 18
        echo "    <main id=\"main\" class=\"page-content home-content\">
      <a id=\"main-content\" tabindex=\"-1\"></a>";
        // line 20
        echo "      ";
        $this->loadTemplate("@vani/template-parts/content_top.html.twig", "themes/contrib/vani/templates/layout/page--front.html.twig", 20)->display($context);
        // line 21
        echo "      ";
        echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, ($context["page"] ?? null), "content", [], "any", false, false, true, 21), 21, $this->source), "html", null, true);
        echo "
      ";
        // line 22
        $this->loadTemplate("@vani/template-parts/content_bottom.html.twig", "themes/contrib/vani/templates/layout/page--front.html.twig", 22)->display($context);
        // line 23
        echo "    </main>
    ";
        // line 24
        if (($context["front_sidebar"] ?? null)) {
            // line 25
            echo "      ";
            $this->loadTemplate("@vani/template-parts/sidebar_left.html.twig", "themes/contrib/vani/templates/layout/page--front.html.twig", 25)->display($context);
            // line 26
            echo "      ";
            $this->loadTemplate("@vani/template-parts/sidebar_right.html.twig", "themes/contrib/vani/templates/layout/page--front.html.twig", 26)->display($context);
            // line 27
            echo "      </div> ";
            // line 28
            echo "    ";
        }
        // line 29
        echo "  </div> ";
        // line 30
        echo "</div>";
        // line 31
        $this->loadTemplate("@vani/template-parts/footer.html.twig", "themes/contrib/vani/templates/layout/page--front.html.twig", 31)->display($context);
        // line 32
        if (($context["slider_show"] ?? null)) {
            // line 33
            echo "  ";
            echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->extensions['Drupal\Core\Template\TwigExtension']->attachLibrary("vani/slider"), "html", null, true);
            echo "
";
        }
    }

    public function getTemplateName()
    {
        return "themes/contrib/vani/templates/layout/page--front.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  92 => 33,  90 => 32,  88 => 31,  86 => 30,  84 => 29,  81 => 28,  79 => 27,  76 => 26,  73 => 25,  71 => 24,  68 => 23,  66 => 22,  61 => 21,  58 => 20,  55 => 18,  51 => 16,  49 => 15,  46 => 14,  44 => 13,  41 => 12,  39 => 11,);
    }

    public function getSourceContext()
    {
        return new Source("", "themes/contrib/vani/templates/layout/page--front.html.twig", "C:\\xampp\\htdocs\\drupal\\drupal-10.1.7\\themes\\contrib\\vani\\templates\\layout\\page--front.html.twig");
    }
    
    public function checkSecurity()
    {
        static $tags = array("include" => 11, "if" => 15);
        static $filters = array("escape" => 21);
        static $functions = array("attach_library" => 33);

        try {
            $this->sandbox->checkSecurity(
                ['include', 'if'],
                ['escape'],
                ['attach_library']
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
