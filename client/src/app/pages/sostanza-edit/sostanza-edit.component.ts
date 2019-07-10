// Import Libraries
import { Component, OnInit } from '@angular/core';
import { Location } from '@angular/common';
import { ActivatedRoute } from '@angular/router';
// Import Services
import { SostanzaService } from '../../services/sostanza.service';
import { FrasiHService } from '../../services/frasi-h.service';
import { UserService } from '../../services/user.service';
// Import Models
import { Sostanza } from '../../domain/movarisch_db/sostanza';
import { FrasiH } from '../../domain/movarisch_db/frasi-h';
import { User } from '../../domain/movarisch_db/user';

// START - USED SERVICES
/**
* SostanzaService.create
*	@description CRUD ACTION create
*
* SostanzaService.get
*	@description CRUD ACTION get
*	@param ObjectId id Id 
*
* FrasiHService.list
*	@description CRUD ACTION list
*
* UserService.list
*	@description CRUD ACTION list
*
* SostanzaService.update
*	@description CRUD ACTION update
*	@param ObjectId id Id
*
*/
// END - USED SERVICES

/**
 * This component allows to edit a Sostanza
 */
@Component({
    selector: 'app-sostanza-edit',
    templateUrl: 'sostanza-edit.component.html',
    styleUrls: ['sostanza-edit.component.css']
})
export class SostanzaEditComponent implements OnInit {
    item: Sostanza;
    listFrasiH: FrasiH[];
    listUser: User[];
    model: Sostanza;
    formValid: Boolean;

    constructor(
    private sostanzaService: SostanzaService,
    private frasihService: FrasiHService,
    private userService: UserService,
    private route: ActivatedRoute,
    private location: Location) {
        // Init item
        this.item = new Sostanza();
    }

    /**
     * Init
     */
    ngOnInit() {
        this.route.params.subscribe(param => {
            const id: string = param['id'];
            if (id !== 'new') {
                this.sostanzaService.get(id).subscribe(item => this.item = item);
            }
            // Get relations
            this.frasihService.list().subscribe(list => this.listFrasiH = list);
            this.userService.list().subscribe(list => this.listUser = list);
        });
    }

    /**
     * Check if an FrasiH is in  FrasiH
     *
     * @param {string} id Id of FrasiH to search
     * @returns {boolean} True if it is found
     */
    containFrasiH(id: string): boolean {
        if (!this.item.FrasiH) return false;
        return this.item.FrasiH.indexOf(id) !== -1;
    }

    /**
     * Add FrasiH from Sostanza
     *
     * @param {string} id Id of FrasiH to add in this.item.FrasiH array
     */
    addFrasiH(id: string) {
        if (!this.item.FrasiH)
            this.item.FrasiH = [];
        this.item.FrasiH.push(id);
    }

    /**
     * Remove an FrasiH from a Sostanza
     *
     * @param {number} index Index of FrasiH in this.item.FrasiH array
     */
    removeFrasiH(index: number) {
        this.item.FrasiH.splice(index, 1);
    }

    /**
     * Save Sostanza
     *
     * @param {boolean} formValid Form validity check
     * @param Sostanza item Sostanza to save
     */
    save(formValid: boolean, item: Sostanza): void {
        this.formValid = formValid;
        if (formValid) {
            if (item._id) {
                this.sostanzaService.update(item).subscribe(data => this.goBack());
            } else {
                this.sostanzaService.create(item).subscribe(data => this.goBack());
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



