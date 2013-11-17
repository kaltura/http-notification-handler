<%@page import="java.util.HashMap"%>
<%@page import="java.util.Map"%>
<%@ page import = "lib.Kaltura.Sync" %>
<%@ page language="java" contentType="text/html; charset=ISO-8859-1"
    pageEncoding="ISO-8859-1"%>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">
<title>Sync</title>
</head>
<body>
<%

	Map<String, String[]> parameters = request.getParameterMap();

	Map<String, String> newParams = new HashMap<String, String>();
	for(Map.Entry<String, String[]> param : parameters.entrySet())
	{
		String[] strArr = param.getValue();
		String value = "";
		if(strArr.length == 1)
			value = strArr[0];

		newParams.put(param.getKey(), value);
	}

	Sync.sync(newParams);
%>
</body>
</html>