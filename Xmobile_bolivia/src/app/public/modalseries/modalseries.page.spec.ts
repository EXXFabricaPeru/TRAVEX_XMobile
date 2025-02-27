import { CUSTOM_ELEMENTS_SCHEMA } from '@angular/core';
import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { ModalseriesPage } from './modalseries.page';

describe('ModalseriesPage', () => {
  let component: ModalseriesPage;
  let fixture: ComponentFixture<ModalseriesPage>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ ModalseriesPage ],
      schemas: [CUSTOM_ELEMENTS_SCHEMA],
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(ModalseriesPage);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
