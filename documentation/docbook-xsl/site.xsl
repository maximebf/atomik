<?xml version='1.0'?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">

	<xsl:import href="common.xsl"/>

	<xsl:param name="atomik.base" select="'..'" />
	<xsl:param name="atomik.stylesheet" select="'manual.css'" />
	<xsl:param name="html.stylesheet" select="concat($atomik.base, '/assets/css/main.css ', $atomik.stylesheet)"/>
	
	<xsl:template name="user.header.navigation">
		<div id="header-wrapper">
			<div id="header">
				<h1>
					<span>Atomik Framework</span>
				</h1>
				<ul id="menu">
					<li>
						<a href="http://code.google.com/p/atomikframework">Code</a>
					</li>
					<li>
						<a><xsl:attribute name="href"><xsl:value-of select="concat($atomik.base, '/plugins')" /></xsl:attribute>Plugins</a>
					</li>
					<li>
						<a><xsl:attribute name="href"><xsl:value-of select="concat($atomik.base, '/community')" /></xsl:attribute>Community</a>
					</li>
					<li class="selected">
						<a><xsl:attribute name="href"><xsl:value-of select="concat($atomik.base, '/docs')" /></xsl:attribute>Documentation</a>
					</li>
					<li>
						<a><xsl:attribute name="href"><xsl:value-of select="concat($atomik.base, '/download')" /></xsl:attribute>Download</a>
					</li>
					<li>
						<a><xsl:attribute name="href"><xsl:value-of select="concat($atomik.base, '/index')" /></xsl:attribute>Home</a>
					</li>
				</ul>
				<div class="clear"></div>
			</div>
			<div id="menu-bottom"></div>
		</div>
	</xsl:template>
	
	<xsl:template name="user.footer.navigation">
			<div id="footer">
				<p>
					<img alt="Creative Commons Attrution license">
						<xsl:attribute name="src"><xsl:value-of select="concat($atomik.base, '/assets/images/creative-commons.png')" /></xsl:attribute>
					</img><br />
					The manual is licensed under the Creative Commons Attribution license.
				</p>
				<p>
					Atomik Framework © 2008 - 2009 <a href="http://www.pimpmycode.fr">Maxime Bouroumeau-Fuseau</a> - 
					<a href="http://www.atomikframework.com">
						<img alt="powered by Atomik">
							<xsl:attribute name="src"><xsl:value-of select="concat($atomik.base, '/assets/images/atomik-powered.png')" /></xsl:attribute>
						</img>
					</a>
					<a href="../behind-the-site" id="behind-the-site" title="Behind the site">
						<span>Behind the site</span>
					</a>
				</p>
			</div>
	</xsl:template>
	
	<xsl:template name="header.navigation">
		
	</xsl:template>

</xsl:stylesheet>
