// Import Libraries
import { Component, OnInit } from '@angular/core';
import { Location } from '@angular/common';
import { ActivatedRoute } from '@angular/router';
// Import Services
import { ProcessoService } from '../../services/processo.service';
// Import Models
import { Processo } from '../../domain/movarisch_db/processo';
import { Sostanza } from '../../domain/movarisch_db/sostanza';

// START - USED SERVICES
/**
* ProcessoService.create
*	@description CRUD ACTION create
*
* ProcessoService.get
*	@description CRUD ACTION get
*	@param ObjectId id Id 
*
* ProcessoService.update
*	@description CRUD ACTION update
*	@param ObjectId id Id
*
*/
// END - USED SERVICES

/**
 * This component allows to edit a Processo
 */
@Component({
    selector: 'app-processo-edit',
    templateUrl: 'processo-edit.component.html',
    styleUrls: ['processo-edit.component.css']
})
export class ProcessoEditComponent implements OnInit {
    item: Processo;
    listSostanza: Sostanza[];
    model: Processo;
    formValid: Boolean;

    constructor(
    private processoService: ProcessoService,
    private route: ActivatedRoute,
    private location: Location) {
        // Init item
        this.item = new Processo();
    }

    /**
     * Init
     */
    ngOnInit() {
        this.route.params.subscribe(param => {
            const id: string = param['id'];
            if (id !== 'new') {
                this.processoService.get(id).subscribe(item => this.item = item);
            }
            // Get relations
            this.sostanzaService.list().subscribe(list => this.listSostanza = list);
        });
    }

    /**
     * Check if an Sostanza is in  Sostanza
     *
     * @param {string} id Id of Sostanza to search
     * @returns {boolean} True if it is found
     */
    containSostanza(id: string): boolean {
        if (!this.item.Sostanza) return false;
        return this.item.Sostanza.indexOf(id) !== -1;
    }

    /**
     * Add Sostanza from Processo
     *
     * @param {string} id Id of Sostanza to add in this.item.Sostanza array
     */
    addSostanza(id: string) {
        if (!this.item.Sostanza)
            this.item.Sostanza = [];
        this.item.Sostanza.push(id);
    }

    /**
     * Remove an Sostanza from a Processo
     *
     * @param {number} index Index of Sostanza in this.item.Sostanza array
     */
    removeSostanza(index: number) {
        this.item.Sostanza.splice(index, 1);
    }

    /**
     * Save Processo
     *
     * @param {boolean} formValid Form validity check
     * @param Processo item Processo to save
     */
    save(formValid: boolean, item: Processo): void {
        this.formValid = formValid;
        if (formValid) {
            if (item._id) {
                this.processoService.update(item).subscribe(data => this.goBack());
            } else {
                this.processoService.create(item).subscribe(data => this.goBack());
            } 
        }
    }

    /**
     * Go Back
     */
    goBack(): void {
        this.location.back();
    }


}



