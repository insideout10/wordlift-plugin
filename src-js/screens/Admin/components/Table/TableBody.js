import React from "react";

const TableBody = ({ rows, TableRow }) => (
  <tbody>{rows.map((value, key) => <TableRow key={key} row={value} />)}</tbody>
);

export default TableBody;
