import { Component } from '@angular/core';
import { OnInit } from '@angular/core';
// Import Services
import { SostanzaService } from '../../services/sostanza.service';
// Import Models
import { Sostanza } from '../../domain/movarisch_db/sostanza';

// START - USED SERVICES
/**
* SostanzaService.delete
*	@description CRUD ACTION delete
*	@param ObjectId id Id
*
* SostanzaService.list
*	@description CRUD ACTION list
*
*/
// END - USED SERVICES

/**
 * This component shows a list of Sostanza
 * @class SostanzaListComponent
 */
@Component({
    selector: 'app-sostanza-list',
    templateUrl: './sostanza-list.component.html',
    styleUrls: ['./sostanza-list.component.css']
})
export class SostanzaListComponent implements OnInit {
    list: Sostanza[];
    search: any = {};
    idSelected: string;
    constructor(
        private sostanzaService: SostanzaService,
    ) { }

    /**
     * Init
     */
    ngOnInit(): void {
        this.sostanzaService.list().subscribe(list => this.list = list);
    }

    /**
     * Select Sostanza to remove
     *
     * @param {string} id Id of the Sostanza to remove
     */
    selectId(id: string) {
        this.idSelected = id;
    }

    /**
     * Remove selected Sostanza
     */
    deleteItem() {
        this.sostanzaService.remove(this.idSelected).subscribe(data => this.list = this.list.filter(el => el._id !== this.idSelected));
    }

}
