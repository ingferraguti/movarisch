import { Component } from '@angular/core';
import { OnInit } from '@angular/core';
// Import Services
import { FrasiHService } from '../../services/frasi-h.service';
// Import Models
import { FrasiH } from '../../domain/movarisch_db/frasi-h';

// START - USED SERVICES
/**
* FrasiHService.delete
*	@description CRUD ACTION delete
*	@param ObjectId id Id
*
* FrasiHService.list
*	@description CRUD ACTION list
*
*/
// END - USED SERVICES

/**
 * This component shows a list of FrasiH
 * @class FrasiHListComponent
 */
@Component({
    selector: 'app-frasi-hlist',
    templateUrl: './frasi-hlist.component.html',
    styleUrls: ['./frasi-hlist.component.css']
})
export class FrasiHListComponent implements OnInit {
    list: FrasiH[];
    search: any = {};
    idSelected: string;
    constructor(
        private frasihService: FrasiHService,
    ) { }

    /**
     * Init
     */
    ngOnInit(): void {
        this.frasihService.list().subscribe(list => this.list = list);
    }

    /**
     * Select FrasiH to remove
     *
     * @param {string} id Id of the FrasiH to remove
     */
    selectId(id: string) {
        this.idSelected = id;
    }

    /**
     * Remove selected FrasiH
     */
    deleteItem() {
        this.frasihService.remove(this.idSelected).subscribe(data => this.list = this.list.filter(el => el._id !== this.idSelected));
    }

}
