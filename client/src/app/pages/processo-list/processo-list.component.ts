import { Component } from '@angular/core';
import { OnInit } from '@angular/core';
// Import Services
import { ProcessoService } from '../../services/processo.service';
// Import Models
import { Processo } from '../../domain/movarisch_db/processo';

// START - USED SERVICES
/**
* ProcessoService.delete
*	@description CRUD ACTION delete
*	@param ObjectId id Id
*
* ProcessoService.list
*	@description CRUD ACTION list
*
*/
// END - USED SERVICES

/**
 * This component shows a list of Processo
 * @class ProcessoListComponent
 */
@Component({
    selector: 'app-processo-list',
    templateUrl: './processo-list.component.html',
    styleUrls: ['./processo-list.component.css']
})
export class ProcessoListComponent implements OnInit {
    list: Processo[];
    search: any = {};
    idSelected: string;
    constructor(
        private processoService: ProcessoService,
    ) { }

    /**
     * Init
     */
    ngOnInit(): void {
        this.processoService.list().subscribe(list => this.list = list);
    }

    /**
     * Select Processo to remove
     *
     * @param {string} id Id of the Processo to remove
     */
    selectId(id: string) {
        this.idSelected = id;
    }

    /**
     * Remove selected Processo
     */
    deleteItem() {
        this.processoService.remove(this.idSelected).subscribe(data => this.list = this.list.filter(el => el._id !== this.idSelected));
    }

}
