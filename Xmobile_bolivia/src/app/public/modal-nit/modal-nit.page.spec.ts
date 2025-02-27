import { async, ComponentFixture, TestBed } from '@angular/core/testing';
import { IonicModule } from '@ionic/angular';

import { ModalNitPage } from './modal-nit.page';

describe('ModalNitPage', () => {
  let component: ModalNitPage;
  let fixture: ComponentFixture<ModalNitPage>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ ModalNitPage ],
      imports: [IonicModule.forRoot()]
    }).compileComponents();

    fixture = TestBed.createComponent(ModalNitPage);
    component = fixture.componentInstance;
    fixture.detectChanges();
  }));

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
