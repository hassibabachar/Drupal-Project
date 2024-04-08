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

/* core/themes/claro/templates/maintenance-page.html.twig */
class __TwigTemplate_b49fea4f75eaf092a63e1fa88fb2e4e9 extends Template
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
        // line 16
        echo "<div class=\"layout-container\">

  <header role=\"banner\">
    ";
        // line 19
        if (($context["site_name"] ?? null)) {
            // line 20
            echo "      <h2 class=\"site-name\">";
            echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(($context["site_name"] ?? null), 20, $this->source), "html", null, true);
            echo "</h2>
    ";
        }
        // line 22
        echo "  </header>

  ";
        // line 24
        if (twig_get_attribute($this->env, $this->source, ($context["page"] ?? null), "sidebar_first", [], "any", false, false, true, 24)) {
            // line 25
            echo "    <aside class=\"layout-sidebar-first\" role=\"complementary\">
      ";
            // line 26
            echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, ($context["page"] ?? null), "sidebar_first", [], "any", false, false, true, 26), 26, $this->source), "html", null, true);
            echo "
    </aside>";
            // line 28
            echo "  ";
        }
        // line 29
        echo "
  <main role=\"main\" class=\"main-content\">
    ";
        // line 31
        if (($context["title"] ?? null)) {
            // line 32
            echo "      <h1 class=\"title\">";
            echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(($context["title"] ?? null), 32, $this->source), "html", null, true);
            echo "</h1>
    ";
        }
        // line 34
        echo "    ";
        echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, ($context["page"] ?? null), "highlighted", [], "any", false, false, true, 34), 34, $this->source), "html", null, true);
        echo "
    <div class=\"content\">
      ";
        // line 36
        echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, ($context["page"] ?? null), "content", [], "any", false, false, true, 36), 36, $this->source), "html", null, true);
        echo "
    </div>
  </main>

  ";
        // line 40
        if (twig_get_attribute($this->env, $this->source, ($context["page"] ?? null), "page_bottom", [], "any", false, false, true, 40)) {
            // line 41
            echo "    <footer role=\"contentinfo\">
      ";
            // line 42
            echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, ($context["page"] ?? null), "page_bottom", [], "any", false, false, true, 42), 42, $this->source), "html", null, true);
            echo "
    </footer>
  ";
        }
        // line 45
        echo "
</div>";
    }

    public function getTemplateName()
    {
        return "core/themes/claro/templates/maintenance-page.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  104 => 45,  98 => 42,  95 => 41,  93 => 40,  86 => 36,  80 => 34,  74 => 32,  72 => 31,  68 => 29,  65 => 28,  61 => 26,  58 => 25,  56 => 24,  52 => 22,  46 => 20,  44 => 19,  39 => 16,);
    }

    public function getSourceContext()
    {
        return new Source("", "core/themes/claro/templates/maintenance-page.html.twig", "C:\\xampp\\htdocs\\drupal\\drupal-10.1.7\\core\\themes\\claro\\templates\\maintenance-page.html.twig");
    }
    
    public function checkSecurity()
    {
        static $tags = array("if" => 19);
        static $filters = array("escape" => 20);
        static $functions = array();

        try {
            $this->sandbox->checkSecurity(
                ['if'],
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
