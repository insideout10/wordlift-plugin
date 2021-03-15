import TableSection, {TABLE_SECTION_DELIMITER} from "./table-section";

export default class Table {

    constructor(head, body, foot) {
        this.sections = [new TableSection(head), new TableSection(body), new TableSection(foot)]
    }

    getAnalysisHtml() {
        return this.sections.map((section) => {
            return section.getAnalysisHtml()
        }).join("");
    }

    updateFromAnalysisHtml(html) {
        html.split(TABLE_SECTION_DELIMITER)
            .map((section, index) => {
                if (this.sections[index]) {
                    this.sections[index].updateFromAnalysisHtml(section)
                }
            })
    }

    getAttributeData() {
        return {
            head: this.sections[0].getAttributeData(),
            body: this.sections[1].getAttributeData(),
            foot: this.sections[2].getAttributeData(),
        }
    }

}