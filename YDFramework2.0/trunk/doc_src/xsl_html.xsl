<?xml version="1.0"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">

    <xsl:import href="docbook/html/chunk.xsl" />
    <xsl:import href="xsl_common.xsl" />

    <xsl:param name="role">html</xsl:param>

    <xsl:param name="html.stylesheet">style.css</xsl:param>
    <xsl:param name="use.id.as.filename">1</xsl:param>

    <xsl:param name="navig.graphics">1</xsl:param>
    <xsl:param name="navig.graphics.path">./</xsl:param>
    <xsl:param name="navig.graphics.extension">.gif</xsl:param>

    <xsl:template name="nongraphical.admonition">
      <div class="{name(.)}">
        <xsl:if test="$admon.style">
          <xsl:attribute name="style">
            <xsl:value-of select="$admon.style"/>
          </xsl:attribute>
        </xsl:if>
    
        <h3 class="title">
          <xsl:call-template name="anchor"/>
          <xsl:if test="$admon.textlabel != 0 or title">
            <xsl:apply-templates select="." mode="object.title.markup"/>
          </xsl:if>
        </h3>
    
        <xsl:apply-templates/>
      </div>
    </xsl:template>
    
</xsl:stylesheet>
