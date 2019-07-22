// Import Libraries
import { Component, OnInit } from '@angular/core';
import { Location } from '@angular/common';
import { ActivatedRoute } from '@angular/router';
// Import Services
import { MiscelanonpericolosaService } from '../../services/miscelanonpericolosa.service';
import { SostanzaService } from '../../services/sostanza.service';
// Import Models
import { Miscelanonpericolosa } from '../../domain/movarisch_db/miscelanonpericolosa';
import { Sostanza } from '../../domain/movarisch_db/sostanza';

// START - USED SERVICES
/**
* MiscelanonpericolosaService.create
*	@description CRUD ACTION create
*
* MiscelanonpericolosaService.get
*	@description CRUD ACTION get
*	@param ObjectId id Id 
*
* SostanzaService.list
*	@description CRUD ACTION list
*
* MiscelanonpericolosaService.update
*	@description CRUD ACTION update
*	@param ObjectId id Id
*
*/
// END - USED SERVICES

/**
 * This component allows to edit a Miscelanonpericolosa
 */
@Component({
    selector: 'app-miscelanonpericolosa-edit',
    templateUrl: 'miscelanonpericolosa-edit.component.html',
    styleUrls: ['miscelanonpericolosa-edit.component.css']
})
export class MiscelanonpericolosaEditComponent implements OnInit {
    item: Miscelanonpericolosa;
    listSostanza: Sostanza[];
    model: Miscelanonpericolosa;
    formValid: Boolean;

    constructor(
    private miscelanonpericolosaService: MiscelanonpericolosaService,
    private sostanzaService: SostanzaService,
    private route: ActivatedRoute,
    private location: Location) {
        // Init item
        this.item = new Miscelanonpericolosa();
    }

    /**
     * Init
     */
    ngOnInit() {
        this.route.params.subscribe(param => {
            const id: string = param['id'];
            if (id !== 'new') {
                this.miscelanonpericolosaService.get(id).subscribe(item => this.item = item);
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
     * Add Sostanza from Miscelanonpericolosa
     *
     * @param {string} id Id of Sostanza to add in this.item.Sostanza array
     */
    addSostanza(id: string) {
        if (!this.item.Sostanza)
            this.item.Sostanza = [];
        this.item.Sostanza.push(id);
    }

    /**
     * Remove an Sostanza from a Miscelanonpericolosa
     *
     * @param {number} index Index of Sostanza in this.item.Sostanza array
     */
    removeSostanza(index: number) {
        this.item.Sostanza.splice(index, 1);
    }

    /**
     * Save Miscelanonpericolosa
     *
     * @param {boolean} formValid Form validity check
     * @param Miscelanonpericolosa item Miscelanonpericolosa to save
     */
    save(formValid: boolean, item: Miscelanonpericolosa): void {
        this.formValid = formValid;
        if (formValid) {
            if (item._id) {
                this.miscelanonpericolosaService.update(item).subscribe(data => this.goBack());
            } else {
                this.miscelanonpericolosaService.create(item).subscribe(data => this.goBack());
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



