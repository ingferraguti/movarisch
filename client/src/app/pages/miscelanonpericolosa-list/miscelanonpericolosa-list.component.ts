import { Component } from '@angular/core';
import { OnInit } from '@angular/core';
// Import Services
import { MiscelanonpericolosaService } from '../../services/miscelanonpericolosa.service';
// Import Models
import { Miscelanonpericolosa } from '../../domain/movarisch_db/miscelanonpericolosa';

// START - USED SERVICES
/**
* MiscelanonpericolosaService.delete
*	@description CRUD ACTION delete
*	@param ObjectId id Id
*
* MiscelanonpericolosaService.list
*	@description CRUD ACTION list
*
*/
// END - USED SERVICES

/**
 * This component shows a list of Miscelanonpericolosa
 * @class MiscelanonpericolosaListComponent
 */
@Component({
    selector: 'app-miscelanonpericolosa-list',
    templateUrl: './miscelanonpericolosa-list.component.html',
    styleUrls: ['./miscelanonpericolosa-list.component.css']
})
export class MiscelanonpericolosaListComponent implements OnInit {
    list: Miscelanonpericolosa[];
    search: any = {};
    idSelected: string;
    constructor(
        private miscelanonpericolosaService: MiscelanonpericolosaService,
    ) { }

    /**
     * Init
     */
    ngOnInit(): void {
        this.miscelanonpericolosaService.list().subscribe(list => this.list = list);
    }

    /**
     * Select Miscelanonpericolosa to remove
     *
     * @param {string} id Id of the Miscelanonpericolosa to remove
     */
    selectId(id: string) {
        this.idSelected = id;
    }

    /**
     * Remove selected Miscelanonpericolosa
     */
    deleteItem() {
        this.miscelanonpericolosaService.remove(this.idSelected).subscribe(data => this.list = this.list.filter(el => el._id !== this.idSelected));
    }

}
