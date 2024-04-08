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

/* themes/contrib/vani/templates/layout/page.html.twig */
class __TwigTemplate_85575705eeb57e8c21cee8436ee39da4 extends Template
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
        // line 52
        $this->loadTemplate("@vani/template-parts/header.html.twig", "themes/contrib/vani/templates/layout/page.html.twig", 52)->display($context);
        // line 53
        echo "<div id=\"main-wrapper\" class=\"main-wrapper\">
  <div class=\"container\">
  <div class=\"main-container\">
    <main id=\"main\" class=\"page-content\">
      <a id=\"main-content\" tabindex=\"-1\"></a>";
        // line 58
        echo "      ";
        $this->loadTemplate("@vani/template-parts/content_top.html.twig", "themes/contrib/vani/templates/layout/page.html.twig", 58)->display($context);
        // line 59
        echo "      <div class=\"node-content\">
        ";
        // line 60
        echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, ($context["page"] ?? null), "content", [], "any", false, false, true, 60), 60, $this->source), "html", null, true);
        echo "
      </div>
      ";
        // line 62
        $this->loadTemplate("@vani/template-parts/content_bottom.html.twig", "themes/contrib/vani/templates/layout/page.html.twig", 62)->display($context);
        // line 63
        echo "    </main>
    ";
        // line 64
        $this->loadTemplate("@vani/template-parts/sidebar_left.html.twig", "themes/contrib/vani/templates/layout/page.html.twig", 64)->display($context);
        // line 65
        echo "    ";
        $this->loadTemplate("@vani/template-parts/sidebar_right.html.twig", "themes/contrib/vani/templates/layout/page.html.twig", 65)->display($context);
        // line 66
        echo "  </div> ";
        // line 67
        echo "  </div> ";
        // line 68
        echo "</div>";
        // line 69
        $this->loadTemplate("@vani/template-parts/footer.html.twig", "themes/contrib/vani/templates/layout/page.html.twig", 69)->display($context);
    }

    public function getTemplateName()
    {
        return "themes/contrib/vani/templates/layout/page.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  74 => 69,  72 => 68,  70 => 67,  68 => 66,  65 => 65,  63 => 64,  60 => 63,  58 => 62,  53 => 60,  50 => 59,  47 => 58,  41 => 53,  39 => 52,);
    }

    public function getSourceContext()
    {
        return new Source("", "themes/contrib/vani/templates/layout/page.html.twig", "C:\\xampp\\htdocs\\drupal\\drupal-10.1.7\\themes\\contrib\\vani\\templates\\layout\\page.html.twig");
    }
    
    public function checkSecurity()
    {
        static $tags = array("include" => 52);
        static $filters = array("escape" => 60);
        static $functions = array();

        try {
            $this->sandbox->checkSecurity(
                ['include'],
                ['escape'],
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
