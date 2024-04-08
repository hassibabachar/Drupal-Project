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

/* core/themes/stable9/templates/content-edit/filter-tips.html.twig */
class __TwigTemplate_e886fb15a6d79ac0eec63f43c1fb8bde extends Template
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
        // line 19
        if (($context["multiple"] ?? null)) {
            // line 20
            echo "  <h2>";
            echo $this->extensions['Drupal\Core\Template\TwigExtension']->renderVar(t("Text Formats"));
            echo "</h2>
";
        }
        // line 22
        echo "
";
        // line 23
        if (twig_length_filter($this->env, ($context["tips"] ?? null))) {
            // line 24
            echo "  ";
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable(($context["tips"] ?? null));
            foreach ($context['_seq'] as $context["name"] => $context["tip"]) {
                // line 25
                echo "
    ";
                // line 26
                if (($context["multiple"] ?? null)) {
                    // line 27
                    echo "      <div";
                    echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(($context["attributes"] ?? null), 27, $this->source), "html", null, true);
                    echo ">
      <h3>";
                    // line 28
                    echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, $context["tip"], "name", [], "any", false, false, true, 28), 28, $this->source), "html", null, true);
                    echo "</h3>
    ";
                }
                // line 30
                echo "
    ";
                // line 31
                if (twig_length_filter($this->env, twig_get_attribute($this->env, $this->source, $context["tip"], "list", [], "any", false, false, true, 31))) {
                    // line 32
                    echo "      <ul>
      ";
                    // line 33
                    $context['_parent'] = $context;
                    $context['_seq'] = twig_ensure_traversable(twig_get_attribute($this->env, $this->source, $context["tip"], "list", [], "any", false, false, true, 33));
                    foreach ($context['_seq'] as $context["_key"] => $context["item"]) {
                        // line 34
                        echo "        <li";
                        echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, $context["tip"], "attributes", [], "any", false, false, true, 34), 34, $this->source), "html", null, true);
                        echo ">";
                        echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, $context["item"], "tip", [], "any", false, false, true, 34), 34, $this->source), "html", null, true);
                        echo "</li>
      ";
                    }
                    $_parent = $context['_parent'];
                    unset($context['_seq'], $context['_iterated'], $context['_key'], $context['item'], $context['_parent'], $context['loop']);
                    $context = array_intersect_key($context, $_parent) + $_parent;
                    // line 36
                    echo "      </ul>
    ";
                }
                // line 38
                echo "
    ";
                // line 39
                if (($context["multiple"] ?? null)) {
                    // line 40
                    echo "      </div>
    ";
                }
                // line 42
                echo "
  ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['name'], $context['tip'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
        }
    }

    public function getTemplateName()
    {
        return "core/themes/stable9/templates/content-edit/filter-tips.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  108 => 42,  104 => 40,  102 => 39,  99 => 38,  95 => 36,  84 => 34,  80 => 33,  77 => 32,  75 => 31,  72 => 30,  67 => 28,  62 => 27,  60 => 26,  57 => 25,  52 => 24,  50 => 23,  47 => 22,  41 => 20,  39 => 19,);
    }

    public function getSourceContext()
    {
        return new Source("", "core/themes/stable9/templates/content-edit/filter-tips.html.twig", "C:\\xampp\\htdocs\\drupal\\drupal-10.1.7\\core\\themes\\stable9\\templates\\content-edit\\filter-tips.html.twig");
    }
    
    public function checkSecurity()
    {
        static $tags = array("if" => 19, "for" => 24);
        static $filters = array("t" => 20, "length" => 23, "escape" => 27);
        static $functions = array();

        try {
            $this->sandbox->checkSecurity(
                ['if', 'for'],
                ['t', 'length', 'escape'],
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
