import { CUSTOM_ELEMENTS_SCHEMA } from '@angular/core';
import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { VisitasPage } from './visitas.page';

describe('VisitasPage', () => {
  let component: VisitasPage;
  let fixture: ComponentFixture<VisitasPage>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ VisitasPage ],
      schemas: [CUSTOM_ELEMENTS_SCHEMA],
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(VisitasPage);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
