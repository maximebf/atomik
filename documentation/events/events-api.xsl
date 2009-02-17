<?xml version='1.0'?>
<xsl:stylesheet version="1.0" 
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
	xmlns:xi="http://www.w3.org/2001/XInclude"
	xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
	exclude-result-prefixes="xi xsi">
	
	<xsl:output
		method="xml"
		indent="yes" />
	
	<xsl:template match="/events">
		<events>
			<xsl:for-each select="//event">
				<xsl:copy-of select="." />
			</xsl:for-each>
		</events>
	</xsl:template>

</xsl:stylesheet>
