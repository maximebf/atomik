<?xml version='1.0'?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">

	<xsl:param name="atomik.base" select="'../..'" />
	<xsl:param name="atomik.stylesheet" select="'manual.css'" />
	<xsl:param name="atomik.menu" select="'doc'" />
	
	<xsl:template name="atomik.header">
		<div id="header-wrapper">
			<div id="header">
				<h1>
					<span>Atomik Framework</span>
				</h1>
				<ul id="menu">
					<li>
						<a><xsl:attribute name="href"><xsl:value-of select="concat($atomik.base, '/index')" /></xsl:attribute>Home</a>
					</li>
					<li>
						<a><xsl:attribute name="href"><xsl:value-of select="concat($atomik.base, '/news')" /></xsl:attribute>News</a>
					</li>
					<li>
						<a><xsl:attribute name="href"><xsl:value-of select="concat($atomik.base, '/download')" /></xsl:attribute>Download</a>
					</li>
					<li>
						<xsl:if test="$atomik.menu = 'get-started'">
							<xsl:attribute name="class">selected</xsl:attribute>
						</xsl:if>
						<a><xsl:attribute name="href"><xsl:value-of select="concat($atomik.base, '/get-started')" /></xsl:attribute>Get started</a>
					</li>
					<li>
						<xsl:if test="$atomik.menu = 'doc'">
							<xsl:attribute name="class">selected</xsl:attribute>
						</xsl:if>
						<a><xsl:attribute name="href"><xsl:value-of select="concat($atomik.base, '/docs')" /></xsl:attribute>Documentation</a>
					</li>
					<li>
						<a><xsl:attribute name="href"><xsl:value-of select="concat($atomik.base, '/community')" /></xsl:attribute>Community</a>
					</li>
					<li>
						<a><xsl:attribute name="href"><xsl:value-of select="concat($atomik.base, '/plugins')" /></xsl:attribute>Plugins</a>
					</li>
					<li>
						<a href="http://code.google.com/p/atomikframework">Code</a>
					</li>
				</ul>
				<div class="clear"></div>
			</div>
			<div id="menu-bottom"></div>
		</div>
	</xsl:template>
	
	<xsl:template name="atomik.footer">
		<div id="footer">
			<p>
				<p>
					This documentation is licensed under the Creative Commons Attribution 3.0 License.
				</p>
				<a href="http://creativecommons.org/licenses/by/3.0/">
					<img alt="Creative Commons Attrution license">
						<xsl:attribute name="src"><xsl:value-of select="concat($atomik.base, '/assets/images/creative-commons.png')" /></xsl:attribute>
					</img>
				</a>
			</p>
			<p>
				Atomik Framework © 2008 - 2010 <a href="http://www.pimpmycode.fr">Maxime Bouroumeau-Fuseau</a> - 
				<a href="http://www.atomikframework.com">
					<img alt="powered by Atomik">
						<xsl:attribute name="src"><xsl:value-of select="concat($atomik.base, '/assets/images/atomik-powered.png')" /></xsl:attribute>
					</img>
				</a>
				<a id="behind-the-site" title="Behind the site">
					<xsl:attribute name="href"><xsl:value-of select="concat($atomik.base, '/behind-the-site')" /></xsl:attribute>
					<span>Behind the site</span>
				</a>
			</p>
		</div>
		<script type="text/javascript" src="http://alexgorbatchev.com/pub/sh/2.1.364/scripts/shCore.js"></script>
		<script type="text/javascript" src="http://alexgorbatchev.com/pub/sh/2.1.364/scripts/shBrushPhp.js"></script>
		<script type="text/javascript">SyntaxHighlighter.all()</script>
	</xsl:template>

</xsl:stylesheet>
