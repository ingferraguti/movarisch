// Import Libraries
import { Component, OnInit } from '@angular/core';
import { Location } from '@angular/common';
import { ActivatedRoute } from '@angular/router';
// Import Services
import { FrasiHService } from '../../services/frasi-h.service';
import { SostanzaService } from '../../services/sostanza.service';
// Import Models
import { FrasiH } from '../../domain/movarisch_db/frasi-h';
import { Sostanza } from '../../domain/movarisch_db/sostanza';

// START - USED SERVICES
/**
* FrasiHService.create
*	@description CRUD ACTION create
*
* SostanzaService.findByFrasiH
*	@description CRUD ACTION findByFrasiH
*	@param Objectid key Id della risorsa FrasiH da cercare
*
* FrasiHService.get
*	@description CRUD ACTION get
*	@param ObjectId id Id FrasiH
*	@returns FrasiH
*
* FrasiHService.update
*	@description CRUD ACTION update
*	@param ObjectId id Id
*
*/
// END - USED SERVICES

/**
 * This component allows to edit a FrasiH
 */
@Component({
    selector: 'app-frasi-hedit',
    templateUrl: 'frasi-hedit.component.html',
    styleUrls: ['frasi-hedit.component.css']
})
export class FrasiHEditComponent implements OnInit {
    item: FrasiH;
    listFrasiH: FrasiH[];
    externalSostanza: Sostanza[];
    model: FrasiH;
    formValid: Boolean;

    constructor(
    private frasihService: FrasiHService,
    private sostanzaService: SostanzaService,
    private route: ActivatedRoute,
    private location: Location) {
        // Init item
        this.item = new FrasiH();
        this.externalSostanza = [];
    }

    /**
     * Init
     */
    ngOnInit() {
        this.route.params.subscribe(param => {
            const id: string = param['id'];
            if (id !== 'new') {
                this.frasihService.get(id).subscribe(item => this.item = item);
                this.sostanzaService.findByFrasiH(id).subscribe(list => this.externalSostanza = list);
            }
            // Get relations
        });
    }


    /**
     * Save FrasiH
     *
     * @param {boolean} formValid Form validity check
     * @param FrasiH item FrasiH to save
     */
    save(formValid: boolean, item: FrasiH): void {
        this.formValid = formValid;
        if (formValid) {
            if (item._id) {
                this.frasihService.update(item).subscribe(data => this.goBack());
            } else {
                this.frasihService.create(item).subscribe(data => this.goBack());
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



