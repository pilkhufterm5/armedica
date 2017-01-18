// ** I18N

// Calendar EN language
// Author: Mihai Bazon, <mishoo@infoiasi.ro>
// Encoding: any
// Distributed under the same terms as the calendar itself.

// For translators: please use UTF-8 if possible.  We strongly believe that
// Unicode is the answer to a real internationalized world.  Also please
// include your contact information in the header, as can be seen above.

// full day names
Calendar._DN = new Array
("�P����",
 "�P���@",
 "�P���G",
 "�P���T",
 "�P���|",
 "�P����",
 "�P����",
 "�P����");

// Please note that the following array of short day names (and the same goes
// for short month names, _SMN) isn't absolutely necessary.  We give it here
// for exemplification on how one can customize the short day names, but if
// they are simply the first N letters of the full name you can simply say:
//
//   Calendar._SDN_len = N; // short day name length
//   Calendar._SMN_len = N; // short month name length
//
// If N = 3 then this is not needed either since we assume a value of 3 if not
// present, to be compatible with translation files that were written before
// this feature.

// short day names
Calendar._SDN = new Array
("��",
 "�@",
 "�G",
 "�T",
 "�|",
 "��",
 "��",
 "��");

// full month names
Calendar._MN = new Array
("�@��",
 "�G��",
 "�T��",
 "�|��",
 "����",
 "����",
 "�C��",
 "�K��",
 "�E��",
 "�Q��",
 "�Q�@��",
 "�Q�G��");

// short month names
Calendar._SMN = new Array
("1��",
 "2��",
 "3��",
 "4��",
 "5��",
 "6��",
 "7��",
 "8��",
 "9��",
 "10��",
 "11��",
 "12��");

// tooltips
Calendar._TT = {};
Calendar._TT["INFO"] = "����'���'";

Calendar._TT["ABOUT"] =
"DHTML Date/Time Selector\n" +
"(c) dynarch.com 2002-2003\n" + // don't translate this this ;-)
"For latest version visit: http://dynarch.com/mishoo/calendar.epl\n" +
"Distributed under GNU LGPL.  See http://gnu.org/licenses/lgpl.html for details." +
"\n\n" +
"Date selection:\n" +
"- Use the \xab, \xbb buttons to select year\n" +
"- Use the " + String.fromCharCode(0x2039) + ", " + String.fromCharCode(0x203a) + " buttons to select month\n" +
"- Hold mouse button on any of the above buttons for faster selection.";
Calendar._TT["ABOUT_TIME"] = "\n\n" +
"Time selection:\n" +
"- Click on any of the time parts to increase it\n" +
"- or Shift-click to decrease it\n" +
"- or click and drag for faster selection.";

Calendar._TT["PREV_YEAR"] = "�h�~ (hold for menu)";
Calendar._TT["PREV_MONTH"] = "�W�Ӥ� (hold for menu)";
Calendar._TT["GO_TODAY"] = "���줵��";
Calendar._TT["NEXT_MONTH"] = "�U�Ӥ� (hold for menu)";
Calendar._TT["NEXT_YEAR"] = "���~ (hold for menu)";
Calendar._TT["SEL_DATE"] = "��ܤ��";
Calendar._TT["DRAG_TO_MOVE"] = "Drag to move";
Calendar._TT["PART_TODAY"] = " (����)";
Calendar._TT["MON_FIRST"] = "����ܬP���@";
Calendar._TT["SUN_FIRST"] = "����ܬP����";
Calendar._TT["CLOSE"] = "����";
Calendar._TT["TODAY"] = "����";
Calendar._TT["TIME_PART"] = "(Shift-)Click or drag to change value";

// date formats
Calendar._TT["DEF_DATE_FORMAT"] = "%Y-%m-%d";
Calendar._TT["TT_DATE_FORMAT"] = "%a, %b %e";

Calendar._TT["WK"] = "�P��";