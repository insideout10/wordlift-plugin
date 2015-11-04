describe "Traslator tests", ->
  it "detect text pos from html one properly - case 1", ->
  	t = Traslator.create '''
 		<p>Sto collaborando con <span id="urn:enhancement-f324418b-8502-2f23-54a8-0e3b90910acf" class="textannotation highlight wl-organization" itemid="http://data.redlink.io/685/dataset-for-fun/entity/Associazione_Sportiva_Roma">Roma</span> adesso.</p>
  	''' 	
  	expect(t.html2text(24)).toBe(21)

  it "detect text pos from html one properly - case 2", ->
   t = Traslator.create '''
 		<p>Sto collaborando con <strong>*<span id="urn:enhancement-86adde59-0246-c85c-1985-fefc0f1d2efd" class="textannotation highlight wl-organization" itemscope="itemscope" itemid="http://dbpedia.org/resource/A.S._Roma">Roma</span></strong> adesso.</p>
   '''
   expect(t.html2text(33)).toBe(22)

  it "detect text pos from html one properly - case 3", ->
   t = Traslator.create '''
 		<p>Sto collaborando con <strong><span id="urn:enhancement-86adde59-0246-c85c-1985-fefc0f1d2efd" class="textannotation highlight wl-organization" itemscope="itemscope" itemid="http://dbpedia.org/resource/A.S._Roma">Roma</span></strong> adesso.</p>
   '''
   expect(t.html2text(32)).toBe(21)

  it "detect text pos from html one properly - case 4 (one line)", ->
   t = Traslator.create '''
<div class="dnd-atom-wrapper type-image context-side_image atom-align-right" contenteditable="false"> <div class="dnd-drop-wrapper"></div> <div class="dnd-legend-wrapper"> <div class="caption"><span id="urn:enhancement-9de5d4e0-a428-4ece-a9b3-8792ad667ffb" class="textannotation">Planning</span> <span id="urn:enhancement-26f94354-a8ba-f73a-d3a8-fac1e5950fae" class="textannotation">for</span> <span id="urn:enhancement-16b8bd6e-4bee-f1e4-d19c-021e1fe55936" class="textannotation disambiguated wl-organization" itemid="http://data.redlink.io/91/be2/entity/NASA">NASA</span>'s.</div> <div class="link"></div> </div> </div>
'''
   expect(t.html2text(562)).toBe(16)

  it "detect text pos from html one properly - case 5 (multiple line)", ->
   content = '''
<div class="dnd-atom-wrapper type-image context-side_image atom-align-right" contenteditable="false">
<div class="dnd-drop-wrapper"></div>
<div class="dnd-legend-wrapper">
<div class="caption"><span id="urn:enhancement-9de5d4e0-a428-4ece-a9b3-8792ad667ffb" class="textannotation">Planning</span> <span id="urn:enhancement-26f94354-a8ba-f73a-d3a8-fac1e5950fae" class="textannotation">for</span> <span id="urn:enhancement-16b8bd6e-4bee-f1e4-d19c-021e1fe55936" class="textannotation disambiguated wl-organization" itemid="http://data.redlink.io/91/be2/entity/NASA">NASA</span>'s.</div>
<div class="link"></div>
</div>
</div>
'''
   t = Traslator.create content
   expect(t.html2text(562)).toBe(16)

  it "detect text pos from html one properly - case 6 (as returned in raw format)", ->
   t = Traslator.create '''
<div class="dnd-atom-wrapper type-image context-side_image atom-align-right" contenteditable="false"><div class="dnd-drop-wrapper">&nbsp;<br></div><div class="dnd-legend-wrapper"><div class="caption">Planning for <span id="urn:enhancement-76811302-ab1e-4a10-9b3e-fe0f990918b7" class="textannotation disambiguated wl-organization" itemid="http://data.redlink.io/91/be2/entity/NASA">NASA</span>'s.</div><div class="link">&nbsp;<br></div></div></div><p>&nbsp;<br></p>
'''
   expect(t.html2text(381)).toBe(14)

  it "detect text pos from html one properly - case 7 (with a named html entity)", ->
   content = '''
Sono andato da Bogot&agrave; a <span>Roma</span>
'''
   t = Traslator.create content
   expect(t.html2text(37)).toBe(24)
   expect(t.getHtml()).toBe(content)
   expect(t.getText()).toBe('Sono andato da Bogotà a Roma')

  it "detect text pos from html one properly - case 8 (with a numered html entity)", ->
   content = '''
Sono andato da Bogot&#224; a <span>Roma</span>
'''
   t = Traslator.create content
   expect(t.html2text(35)).toBe(24)
   expect(t.getHtml()).toBe(content)
   expect(t.getText()).toBe('Sono andato da Bogotà a Roma')

  it "detect text pos from html one properly - case 9 (with an hashtag)", ->
   content = '''
Ecco il mio hashtag #daisempre <span>Roma</span>
'''
   t = Traslator.create content
   #expect(t.html2text(38)).toBe(32)
   expect(t.getHtml()).toBe(content)
   expect(t.getText()).toBe("Ecco il mio hashtag #daisempre Roma")
