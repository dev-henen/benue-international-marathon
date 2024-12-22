<?php

use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Extension\CoreExtension;
use Twig\Extension\SandboxExtension;
use Twig\Markup;
use Twig\Sandbox\SecurityError;
use Twig\Sandbox\SecurityNotAllowedTagError;
use Twig\Sandbox\SecurityNotAllowedFilterError;
use Twig\Sandbox\SecurityNotAllowedFunctionError;
use Twig\Source;
use Twig\Template;
use Twig\TemplateWrapper;

/* base.twig */
class __TwigTemplate_0e17819f2e47ace09ec2d7ae7e6ce3ac extends Template
{
    private Source $source;
    /**
     * @var array<string, Template>
     */
    private array $macros = [];

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->parent = false;

        $this->blocks = [
            'title' => [$this, 'block_title'],
            'content' => [$this, 'block_content'],
        ];
    }

    protected function doDisplay(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        // line 1
        yield "<!DOCTYPE html>
<html>
\t<head>
\t\t<meta charset=\"UTF-8\">
\t\t<meta http-equiv=\"X-UA-Compatible\" content=\"IE=edge\">
\t\t<meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">
\t\t<title>
\t\t\t";
        // line 8
        yield from $this->unwrap()->yieldBlock('title', $context, $blocks);
        // line 10
        yield "\t\t</title>
\t\t<link rel=\"stylesheet\" href=\"/assets/bootstrap/bootstrap-icons.css\" type=\"text/css\"/>
\t\t<link rel=\"stylesheet\" href=\"/assets/css/layout.css\" type=\"text/css\"/>
\t</head>
\t<body>
\t\t<div class=\"top\">
\t\t\t<div class=\"header\">
\t\t\t\t<ul>
\t\t\t\t\t<li>
\t\t\t\t\t\t<img src=\"/assets/images/logo.png\" alt=\"TU MARATHON LOGO\" onclick=\"window.location.href='/';\">
\t\t\t\t\t</li>
\t\t\t\t\t<li>
\t\t\t\t\t\t<a href=\"#\" title=\"Facebook\">
\t\t\t\t\t\t\t<i class=\"bi bi-facebook\"></i>
\t\t\t\t\t\t</a>
\t\t\t\t\t</li>
\t\t\t\t\t<li>
\t\t\t\t\t\t<a href=\"#\" title=\"Twitter\">
\t\t\t\t\t\t\t<i class=\"bi bi-twitter\"></i>
\t\t\t\t\t\t</a>
\t\t\t\t\t</li>
\t\t\t\t\t<li>
\t\t\t\t\t\t<a href=\"#\" title=\"YouTube\">
\t\t\t\t\t\t\t<i class=\"bi bi-youtube\"></i>
\t\t\t\t\t\t</a>
\t\t\t\t\t</li>
\t\t\t\t\t<li>
\t\t\t\t\t\t<a href=\"#\" title=\"Pinterest\">
\t\t\t\t\t\t\t<i class=\"bi bi-pinterest\"></i>
\t\t\t\t\t\t</a>
\t\t\t\t\t</li>
\t\t\t\t\t<li>
\t\t\t\t\t\t<button title=\"Login\" onclick=\"openLogger();\">
\t\t\t\t\t\t\t<i class=\"bi bi-person\"></i>Admin Login
\t\t\t\t\t\t</button>
\t\t\t\t\t</li>
\t\t\t\t\t<li>
\t\t\t\t\t\t<button title=\"Sign up\" onclick=\"window.location.href='/register/#main';\">
\t\t\t\t\t\t\t<i class=\"bi bi-cart\"></i>
\t\t\t\t\t\t\tRegister
\t\t\t\t\t\t</button>
\t\t\t\t\t</li>
\t\t\t\t</ul>
\t\t\t</div>
\t\t\t<nav class=\"top-navigation\">
\t\t\t\t<div class=\"mobile\">
\t\t\t\t\t<span class=\"menuText\">MENU</span>
\t\t\t\t\t<button onclick=\"searchBox()\" title=\"Search\">
\t\t\t\t\t\t<i class=\"bi bi-search\"></i>
\t\t\t\t\t</button>
\t\t\t\t\t<button onclick=\"menu()\" title=\"Menu\">
\t\t\t\t\t\t<i class=\"bi bi-list\"></i>
\t\t\t\t\t</button>
\t\t\t\t</div>
\t\t\t\t<ul id=\"menuLinks\">
\t\t\t\t\t<li>
\t\t\t\t\t\t<a href=\"/\">Home</a>
\t\t\t\t\t</li>
\t\t\t\t\t<li>
\t\t\t\t\t\t<a href=\"/about.php\">About</a>
\t\t\t\t\t</li>
\t\t\t\t\t<li>
\t\t\t\t\t\t<a href=\"/register/lost_my_slip/\">Get slip</a>
\t\t\t\t\t</li>
\t\t\t\t\t<li>
\t\t\t\t\t\t<a href=\"/news/\">News</a>
\t\t\t\t\t</li>
\t\t\t\t\t<li>
\t\t\t\t\t\t<a href=\"/register/\">Register</a>
\t\t\t\t\t</li>
\t\t\t\t\t<li>
\t\t\t\t\t\t<a href=\"/contact.php\">Contact</a>
\t\t\t\t\t</li>
\t\t\t\t</ul>
\t\t\t\t<form id=\"menuSearch\">
\t\t\t\t\t<input type=\"text\" name=\"search\" placeholder=\"Search\">
\t\t\t\t\t<button name=\"submit\">
\t\t\t\t\t\t<i class=\"bi bi-search\"></i>
\t\t\t\t\t</button>
\t\t\t\t</form>
\t\t\t</nav>
\t\t</div>


\t\t";
        // line 94
        yield from $this->unwrap()->yieldBlock('content', $context, $blocks);
        // line 95
        yield "\t</body>
</html>
";
        yield from [];
    }

    // line 8
    /**
     * @return iterable<null|scalar|\Stringable>
     */
    public function block_title(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        yield "Benue International Marathon
\t\t\t";
        yield from [];
    }

    // line 94
    /**
     * @return iterable<null|scalar|\Stringable>
     */
    public function block_content(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        yield from [];
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName(): string
    {
        return "base.twig";
    }

    /**
     * @codeCoverageIgnore
     */
    public function getDebugInfo(): array
    {
        return array (  162 => 94,  150 => 8,  143 => 95,  141 => 94,  55 => 10,  53 => 8,  44 => 1,);
    }

    public function getSourceContext(): Source
    {
        return new Source("<!DOCTYPE html>
<html>
\t<head>
\t\t<meta charset=\"UTF-8\">
\t\t<meta http-equiv=\"X-UA-Compatible\" content=\"IE=edge\">
\t\t<meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">
\t\t<title>
\t\t\t{% block title %}Benue International Marathon
\t\t\t{% endblock %}
\t\t</title>
\t\t<link rel=\"stylesheet\" href=\"/assets/bootstrap/bootstrap-icons.css\" type=\"text/css\"/>
\t\t<link rel=\"stylesheet\" href=\"/assets/css/layout.css\" type=\"text/css\"/>
\t</head>
\t<body>
\t\t<div class=\"top\">
\t\t\t<div class=\"header\">
\t\t\t\t<ul>
\t\t\t\t\t<li>
\t\t\t\t\t\t<img src=\"/assets/images/logo.png\" alt=\"TU MARATHON LOGO\" onclick=\"window.location.href='/';\">
\t\t\t\t\t</li>
\t\t\t\t\t<li>
\t\t\t\t\t\t<a href=\"#\" title=\"Facebook\">
\t\t\t\t\t\t\t<i class=\"bi bi-facebook\"></i>
\t\t\t\t\t\t</a>
\t\t\t\t\t</li>
\t\t\t\t\t<li>
\t\t\t\t\t\t<a href=\"#\" title=\"Twitter\">
\t\t\t\t\t\t\t<i class=\"bi bi-twitter\"></i>
\t\t\t\t\t\t</a>
\t\t\t\t\t</li>
\t\t\t\t\t<li>
\t\t\t\t\t\t<a href=\"#\" title=\"YouTube\">
\t\t\t\t\t\t\t<i class=\"bi bi-youtube\"></i>
\t\t\t\t\t\t</a>
\t\t\t\t\t</li>
\t\t\t\t\t<li>
\t\t\t\t\t\t<a href=\"#\" title=\"Pinterest\">
\t\t\t\t\t\t\t<i class=\"bi bi-pinterest\"></i>
\t\t\t\t\t\t</a>
\t\t\t\t\t</li>
\t\t\t\t\t<li>
\t\t\t\t\t\t<button title=\"Login\" onclick=\"openLogger();\">
\t\t\t\t\t\t\t<i class=\"bi bi-person\"></i>Admin Login
\t\t\t\t\t\t</button>
\t\t\t\t\t</li>
\t\t\t\t\t<li>
\t\t\t\t\t\t<button title=\"Sign up\" onclick=\"window.location.href='/register/#main';\">
\t\t\t\t\t\t\t<i class=\"bi bi-cart\"></i>
\t\t\t\t\t\t\tRegister
\t\t\t\t\t\t</button>
\t\t\t\t\t</li>
\t\t\t\t</ul>
\t\t\t</div>
\t\t\t<nav class=\"top-navigation\">
\t\t\t\t<div class=\"mobile\">
\t\t\t\t\t<span class=\"menuText\">MENU</span>
\t\t\t\t\t<button onclick=\"searchBox()\" title=\"Search\">
\t\t\t\t\t\t<i class=\"bi bi-search\"></i>
\t\t\t\t\t</button>
\t\t\t\t\t<button onclick=\"menu()\" title=\"Menu\">
\t\t\t\t\t\t<i class=\"bi bi-list\"></i>
\t\t\t\t\t</button>
\t\t\t\t</div>
\t\t\t\t<ul id=\"menuLinks\">
\t\t\t\t\t<li>
\t\t\t\t\t\t<a href=\"/\">Home</a>
\t\t\t\t\t</li>
\t\t\t\t\t<li>
\t\t\t\t\t\t<a href=\"/about.php\">About</a>
\t\t\t\t\t</li>
\t\t\t\t\t<li>
\t\t\t\t\t\t<a href=\"/register/lost_my_slip/\">Get slip</a>
\t\t\t\t\t</li>
\t\t\t\t\t<li>
\t\t\t\t\t\t<a href=\"/news/\">News</a>
\t\t\t\t\t</li>
\t\t\t\t\t<li>
\t\t\t\t\t\t<a href=\"/register/\">Register</a>
\t\t\t\t\t</li>
\t\t\t\t\t<li>
\t\t\t\t\t\t<a href=\"/contact.php\">Contact</a>
\t\t\t\t\t</li>
\t\t\t\t</ul>
\t\t\t\t<form id=\"menuSearch\">
\t\t\t\t\t<input type=\"text\" name=\"search\" placeholder=\"Search\">
\t\t\t\t\t<button name=\"submit\">
\t\t\t\t\t\t<i class=\"bi bi-search\"></i>
\t\t\t\t\t</button>
\t\t\t\t</form>
\t\t\t</nav>
\t\t</div>


\t\t{% block content %}{% endblock %}
\t</body>
</html>
", "base.twig", "C:\\Users\\Henen\\Desktop\\Projects\\Repositories\\benue-international-marathon\\app\\templates\\base.twig");
    }
}
