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

/* @vani/template-parts/footer.html.twig */
class __TwigTemplate_f027e4caf25b158a25a772cb415bd7b4 extends Template
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
        echo "<!-- Start: Footer -->
";
        // line 2
        if (twig_get_attribute($this->env, $this->source, ($context["page"] ?? null), "footer_top", [], "any", false, false, true, 2)) {
            // line 3
            echo "  <section id=\"footer-top\">
    <div class=\"footer-top footer-block-region container\">
      ";
            // line 5
            echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, ($context["page"] ?? null), "footer_top", [], "any", false, false, true, 5), 5, $this->source), "html", null, true);
            echo "
    </div>
  </section>
";
        }
        // line 8
        echo "<!-- /footer-top -->
<footer class=\"footer\">
  ";
        // line 10
        if ((((twig_get_attribute($this->env, $this->source, ($context["page"] ?? null), "footer_first", [], "any", false, false, true, 10) || twig_get_attribute($this->env, $this->source, ($context["page"] ?? null), "footer_second", [], "any", false, false, true, 10)) || twig_get_attribute($this->env, $this->source, ($context["page"] ?? null), "footer_third", [], "any", false, false, true, 10)) || twig_get_attribute($this->env, $this->source, ($context["page"] ?? null), "footer_fourth", [], "any", false, false, true, 10))) {
            // line 11
            echo "    <section id=\"footer-blocks\">
      <div class=\"container footer-container\">
        <div class=\"footer-block footer-block-region block-section\">
          ";
            // line 14
            if (twig_get_attribute($this->env, $this->source, ($context["page"] ?? null), "footer_first", [], "any", false, false, true, 14)) {
                // line 15
                echo "            ";
                echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, ($context["page"] ?? null), "footer_first", [], "any", false, false, true, 15), 15, $this->source), "html", null, true);
                echo "
          ";
            }
            // line 17
            echo "        </div> <!--/footer-first -->

        <div class=\"footer-block footer-block-region block-section\">
          ";
            // line 20
            if (twig_get_attribute($this->env, $this->source, ($context["page"] ?? null), "footer_second", [], "any", false, false, true, 20)) {
                // line 21
                echo "            ";
                echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, ($context["page"] ?? null), "footer_second", [], "any", false, false, true, 21), 21, $this->source), "html", null, true);
                echo "
          ";
            }
            // line 23
            echo "        </div> <!--/footer-second -->

        <div class=\"footer-block footer-block-region block-section\">
          ";
            // line 26
            if (twig_get_attribute($this->env, $this->source, ($context["page"] ?? null), "footer_third", [], "any", false, false, true, 26)) {
                // line 27
                echo "            ";
                echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, ($context["page"] ?? null), "footer_third", [], "any", false, false, true, 27), 27, $this->source), "html", null, true);
                echo "
          ";
            }
            // line 29
            echo "        </div> <!--/footer-three -->

        <div class=\"footer-block footer-block-region block-section\">
          ";
            // line 32
            if (twig_get_attribute($this->env, $this->source, ($context["page"] ?? null), "footer_fourth", [], "any", false, false, true, 32)) {
                // line 33
                echo "            ";
                echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, ($context["page"] ?? null), "footer_fourth", [], "any", false, false, true, 33), 33, $this->source), "html", null, true);
                echo "
          ";
            }
            // line 35
            echo "        </div> <!--/footer-fourth -->
      </div> <!--/container -->
    </section> <!--/footer-blocks -->
  ";
        }
        // line 38
        echo " ";
        // line 39
        echo "
  ";
        // line 40
        if (twig_get_attribute($this->env, $this->source, ($context["page"] ?? null), "footer_bottom", [], "any", false, false, true, 40)) {
            // line 41
            echo "    <section id=\"footer-bottom\">
      <div class=\"footer-bottom container block-section\">
        ";
            // line 43
            echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, ($context["page"] ?? null), "footer_bottom", [], "any", false, false, true, 43), 43, $this->source), "html", null, true);
            echo "
      </div> <!-- /.container -->
    </section>
  ";
        }
        // line 46
        echo "<!-- /footer-bottom -->

 ";
        // line 48
        if ((($context["copyright_text"] ?? null) || ($context["all_icons_show"] ?? null))) {
            // line 49
            echo "  <section id=\"footer-bottom-last\">
    <div class=\"container\">
    <div class=\"footer-bottom-last\">
      ";
            // line 52
            if (($context["copyright_text"] ?? null)) {
                // line 53
                echo "          <div class=\"copyright\">
            &copy; ";
                // line 54
                echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, twig_date_format_filter($this->env, "now", "Y"), "html", null, true);
                echo " ";
                echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(($context["site_name"] ?? null), 54, $this->source), "html", null, true);
                echo ", All rights reserved.
          </div>
      ";
            }
            // line 57
            echo "      ";
            if (($context["all_icons_show"] ?? null)) {
                // line 58
                echo "        <div class=\"footer-social-icons\">
          ";
                // line 59
                $this->loadTemplate("@vani/template-parts/social-icons.html.twig", "@vani/template-parts/footer.html.twig", 59)->display($context);
                // line 60
                echo "        </div>
      ";
            }
            // line 62
            echo "      </div> <!--/.social icons -->
    </div> <!-- /.container -->
    </div> <!-- /.container -->
  </section> <!-- /footer-bottom-last -->
  ";
        }
        // line 66
        echo " <!-- end condition if copyright or social icons -->
</footer>
";
        // line 68
        if (($context["scrolltotop_on"] ?? null)) {
            // line 69
            echo "<div class=\"scrolltop\"><div class=\"scrolltop-icon\">&#x2191;</div></div>
";
        }
        // line 71
        echo "<!-- End: Footer -->
";
        // line 72
        if (($context["font_icon_fontawesome"] ?? null)) {
            // line 73
            echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->extensions['Drupal\Core\Template\TwigExtension']->attachLibrary("vani/fontawesome"), "html", null, true);
            echo "
";
        }
        // line 75
        if (($context["font_icon_material"] ?? null)) {
            // line 76
            echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->extensions['Drupal\Core\Template\TwigExtension']->attachLibrary("vani/material"), "html", null, true);
            echo "
";
        }
        // line 78
        if (($context["bootstrapicons"] ?? null)) {
            // line 79
            echo "  ";
            echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->extensions['Drupal\Core\Template\TwigExtension']->attachLibrary("vani/bootstrap-icons"), "html", null, true);
            echo "
";
        }
    }

    public function getTemplateName()
    {
        return "@vani/template-parts/footer.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  209 => 79,  207 => 78,  202 => 76,  200 => 75,  195 => 73,  193 => 72,  190 => 71,  186 => 69,  184 => 68,  180 => 66,  173 => 62,  169 => 60,  167 => 59,  164 => 58,  161 => 57,  153 => 54,  150 => 53,  148 => 52,  143 => 49,  141 => 48,  137 => 46,  130 => 43,  126 => 41,  124 => 40,  121 => 39,  119 => 38,  113 => 35,  107 => 33,  105 => 32,  100 => 29,  94 => 27,  92 => 26,  87 => 23,  81 => 21,  79 => 20,  74 => 17,  68 => 15,  66 => 14,  61 => 11,  59 => 10,  55 => 8,  48 => 5,  44 => 3,  42 => 2,  39 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "@vani/template-parts/footer.html.twig", "C:\\xampp\\htdocs\\drupal\\drupal-10.1.7\\themes\\contrib\\vani\\templates\\template-parts\\footer.html.twig");
    }
    
    public function checkSecurity()
    {
        static $tags = array("if" => 2, "include" => 59);
        static $filters = array("escape" => 5, "date" => 54);
        static $functions = array("attach_library" => 73);

        try {
            $this->sandbox->checkSecurity(
                ['if', 'include'],
                ['escape', 'date'],
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
