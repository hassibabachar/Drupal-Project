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

/* themes/contrib/vani/templates/views/views-view.html.twig */
class __TwigTemplate_7fc9cf191d6df196d1bf838d57a3850e extends Template
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
        // line 34
        $context["classes"] = [0 => "view", 1 => ("view-" . \Drupal\Component\Utility\Html::getClass($this->sandbox->ensureToStringAllowed(        // line 36
($context["id"] ?? null), 36, $this->source)))];
        // line 39
        echo "<div";
        echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, ($context["attributes"] ?? null), "addClass", [0 => ($context["classes"] ?? null)], "method", false, false, true, 39), 39, $this->source), "html", null, true);
        echo ">
  ";
        // line 40
        echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(($context["title_prefix"] ?? null), 40, $this->source), "html", null, true);
        echo "
  ";
        // line 41
        echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(($context["title"] ?? null), 41, $this->source), "html", null, true);
        echo "
  ";
        // line 42
        echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(($context["title_suffix"] ?? null), 42, $this->source), "html", null, true);
        echo "

  ";
        // line 44
        if (($context["header"] ?? null)) {
            // line 45
            echo "    <div class=\"view-header\">
      ";
            // line 46
            echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(($context["header"] ?? null), 46, $this->source), "html", null, true);
            echo "
    </div>
  ";
        }
        // line 49
        echo "
  ";
        // line 50
        if (($context["exposed"] ?? null)) {
            // line 51
            echo "    <div class=\"view-filters\">
      ";
            // line 52
            echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(($context["exposed"] ?? null), 52, $this->source), "html", null, true);
            echo "
    </div>
  ";
        }
        // line 55
        echo "  ";
        if (($context["attachment_before"] ?? null)) {
            // line 56
            echo "    <div class=\"view-attachment view-attachment-before\">
      ";
            // line 57
            echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(($context["attachment_before"] ?? null), 57, $this->source), "html", null, true);
            echo "
    </div>
  ";
        }
        // line 60
        echo "
  ";
        // line 61
        if (($context["rows"] ?? null)) {
            // line 62
            echo "    <div class=\"view-content\">
      ";
            // line 63
            echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(($context["rows"] ?? null), 63, $this->source), "html", null, true);
            echo "
    </div>
  ";
        } elseif (        // line 65
($context["empty"] ?? null)) {
            // line 66
            echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(($context["empty"] ?? null), 66, $this->source), "html", null, true);
            echo "
  ";
        }
        // line 68
        echo "
  ";
        // line 69
        if (($context["pager"] ?? null)) {
            // line 70
            echo "    ";
            echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(($context["pager"] ?? null), 70, $this->source), "html", null, true);
            echo "
  ";
        }
        // line 72
        echo "  ";
        if (($context["attachment_after"] ?? null)) {
            // line 73
            echo "    <div class=\"view-attachment view-attachment-after\">
      ";
            // line 74
            echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(($context["attachment_after"] ?? null), 74, $this->source), "html", null, true);
            echo "
    </div>
  ";
        }
        // line 77
        echo "  ";
        if (($context["more"] ?? null)) {
            // line 78
            echo "    ";
            echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(($context["more"] ?? null), 78, $this->source), "html", null, true);
            echo "
  ";
        }
        // line 80
        echo "
  ";
        // line 81
        if (($context["footer"] ?? null)) {
            // line 82
            echo "    <div class=\"view-footer\">
      ";
            // line 83
            echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(($context["footer"] ?? null), 83, $this->source), "html", null, true);
            echo "
    </div>
  ";
        }
        // line 86
        echo "
  ";
        // line 87
        if (($context["feed_icons"] ?? null)) {
            // line 88
            echo "    <div class=\"feed-icons\">
      ";
            // line 89
            echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(($context["feed_icons"] ?? null), 89, $this->source), "html", null, true);
            echo "
    </div>
  ";
        }
        // line 92
        echo "</div>
";
    }

    public function getTemplateName()
    {
        return "themes/contrib/vani/templates/views/views-view.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  177 => 92,  171 => 89,  168 => 88,  166 => 87,  163 => 86,  157 => 83,  154 => 82,  152 => 81,  149 => 80,  143 => 78,  140 => 77,  134 => 74,  131 => 73,  128 => 72,  122 => 70,  120 => 69,  117 => 68,  112 => 66,  110 => 65,  105 => 63,  102 => 62,  100 => 61,  97 => 60,  91 => 57,  88 => 56,  85 => 55,  79 => 52,  76 => 51,  74 => 50,  71 => 49,  65 => 46,  62 => 45,  60 => 44,  55 => 42,  51 => 41,  47 => 40,  42 => 39,  40 => 36,  39 => 34,);
    }

    public function getSourceContext()
    {
        return new Source("", "themes/contrib/vani/templates/views/views-view.html.twig", "C:\\xampp\\htdocs\\drupal\\drupal-10.1.7\\themes\\contrib\\vani\\templates\\views\\views-view.html.twig");
    }
    
    public function checkSecurity()
    {
        static $tags = array("set" => 34, "if" => 44);
        static $filters = array("clean_class" => 36, "escape" => 39);
        static $functions = array();

        try {
            $this->sandbox->checkSecurity(
                ['set', 'if'],
                ['clean_class', 'escape'],
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
